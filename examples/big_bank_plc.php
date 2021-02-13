<?php

declare(strict_types=1);

/*
 * This file is part of the Structurizr for PHP.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use StructurizrPHP\Client\Client;
use StructurizrPHP\Client\Credentials;
use StructurizrPHP\Client\Infrastructure\Http\SymfonyRequestFactory;
use StructurizrPHP\Client\UrlMap;
use StructurizrPHP\Core\Documentation\Format;
use StructurizrPHP\Core\Documentation\StructurizrDocumentationTemplate;
use StructurizrPHP\Core\Model\Component;
use StructurizrPHP\Core\Model\Enterprise;
use StructurizrPHP\Core\Model\Location;
use StructurizrPHP\Core\Model\Properties;
use StructurizrPHP\Core\Model\Property;
use StructurizrPHP\Core\Model\Relationship;
use StructurizrPHP\Core\Model\Tags;
use StructurizrPHP\Core\View\Configuration\Shape;
use StructurizrPHP\Core\View\PaperSize;
use StructurizrPHP\Core\Workspace;
use Symfony\Component\HttpClient\Psr18Client;

require __DIR__ . '/../vendor/autoload.php';

const EXISTING_SYSTEM_TAG = 'Existing System';
const BANK_STAFF_TAG = 'Bank Staff';
const WEB_BROWSER_TAG = 'Web Browser';
const MOBILE_APP_TAG = 'Mobile App';
const DATABASE_TAG = 'Database';
const FAILOVER_TAG = 'Failover';

$workspace = new Workspace((string) \getenv('STRUCTURIZR_WORKSPACE_ID'), 'Big Bank plc', 'This is an example workspace to illustrate the key features of Structurizr, based around a fictional online banking system.');
$model = $workspace->getModel();
$views = $workspace->getViews();

$model->setEnterprise(new Enterprise('Big Bank plc'));

// people and software systems
$customer = $model->addPerson('Personal Banking Customer', 'A customer of the bank, with personal bank accounts.', Location::external());

$internetBankingSystem = $model->addSoftwareSystem('Internet Banking System', 'Allows customers to view information about their bank accounts, and make payments.', Location::internal());
$customer->uses($internetBankingSystem, 'Views account balances, and makes payments using');

$mainframeBankingSystem = $model->addSoftwareSystem('Mainframe Banking System', 'Stores all of the core banking information about customers, accounts, transactions, etc.', Location::internal());
$mainframeBankingSystem->addTags(EXISTING_SYSTEM_TAG);
$internetBankingSystem->usesSoftwareSystem($mainframeBankingSystem, 'Gets account information from, and makes payments using');

$emailSystem = $model->addSoftwareSystem('E-mail System', 'The internal Microsoft Exchange e-mail system.', Location::internal());
$internetBankingSystem->usesSoftwareSystem($emailSystem, 'Sends e-mail using');
$emailSystem->addTags(EXISTING_SYSTEM_TAG);
$emailSystem->delivers($customer, 'Sends e-mails to');

$atm = $model->addSoftwareSystem('ATM', 'Allows customers to withdraw cash.', Location::internal());
$atm->addTags(EXISTING_SYSTEM_TAG);
$atm->usesSoftwareSystem($mainframeBankingSystem, 'Uses');
$customer->uses($atm, 'Withdraws cash using');

$customerServiceStaff = $model->addPerson('Customer Service Staff', 'Customer service staff within the bank.', Location::internal());
$customerServiceStaff->addTags(BANK_STAFF_TAG);
$customerServiceStaff->uses($mainframeBankingSystem, 'Uses');
$customer->interactsWith($customerServiceStaff, 'Asks questions to', 'Telephone');

$backOfficeStaff = $model->addPerson('Back Office Staff', 'Administration and support staff within the bank.', Location::internal());
$backOfficeStaff->addTags(BANK_STAFF_TAG);
$backOfficeStaff->uses($mainframeBankingSystem, 'Uses');

// containers
$singlePageApplication = $internetBankingSystem->addContainer('Single-Page Application', 'Provides all of the Internet banking functionality to customers via their web browser.', 'JavaScript and Angular');
$singlePageApplication->addTags(WEB_BROWSER_TAG);
$mobileApp = $internetBankingSystem->addContainer('Mobile App', 'Provides a limited subset of the Internet banking functionality to customers via their mobile device.', 'Xamarin');
$mobileApp->addTags(MOBILE_APP_TAG);
$webApplication = $internetBankingSystem->addContainer('Web Application', 'Delivers the static content and the Internet banking single page application.', 'Java and Spring MVC');
$apiApplication = $internetBankingSystem->addContainer('API Application', 'Provides Internet banking functionality via a JSON/HTTPS API.', 'Java and Spring MVC');
$database = $internetBankingSystem->addContainer('Database', 'Stores user registration information, hashed authentication credentials, access logs, etc.', 'Oracle Database Schema');
$database->addTags(DATABASE_TAG);

$customer->uses($webApplication, 'Visits bigbank.com/ib using', 'HTTPS');
$customer->uses($singlePageApplication, 'Views account balances, and makes payments using');
$customer->uses($mobileApp, 'Views account balances, and makes payments using');
$webApplication->uses($singlePageApplication, "Delivers to the customer's web browser");
$apiApplication->uses($database, 'Reads from and writes to', 'JDBC');
$apiApplication->usesSoftwareSystem($mainframeBankingSystem, 'Makes API calls to', 'XML/HTTPS');
$apiApplication->usesSoftwareSystem($emailSystem, 'Sends e-mail using', 'SMTP');

// components
// - for a real-world software system, you would probably want to extract the components using
// - static analysis/reflection rather than manually specifying them all
$signinController = $apiApplication->addComponent('Sign In Controller', 'Allows users to sign in to the Internet Banking System.', '', 'Spring MVC Rest Controller');
$accountsSummaryController = $apiApplication->addComponent('Accounts Summary Controller', 'Provides customers with a summary of their bank accounts.', '', 'Spring MVC Rest Controller');
$resetPasswordController = $apiApplication->addComponent('Reset Password Controller', 'Allows users to reset their passwords with a single use URL.', '', 'Spring MVC Rest Controller');
$securityComponent = $apiApplication->addComponent('Security Component', 'Provides functionality related to signing in, changing passwords, etc.', '', 'Spring Bean');
$mainframeBankingSystemFacade = $apiApplication->addComponent('Mainframe Banking System Facade', 'A facade onto the mainframe banking system.', '', 'Spring Bean');
$emailComponent = $apiApplication->addComponent('E-mail Component', 'Sends e-mails to users.', '', 'Spring Bean');

$controllers = \array_filter(
    $apiApplication->getComponents(),
    function (Component $component) {
        return $component->getTechnology() === 'Spring MVC Rest Controller';
    }
);

foreach ($controllers as $controller) {
    $singlePageApplication->usesComponent($controller, 'Makes API calls to', 'JSON/HTTPS');
    $mobileApp->usesComponent($controller, 'Makes API calls to', 'JSON/HTTPS');
}

$signinController->usesComponent($securityComponent, 'Uses');
$accountsSummaryController->usesComponent($mainframeBankingSystemFacade, 'Uses');
$resetPasswordController->usesComponent($securityComponent, 'Uses');
$resetPasswordController->usesComponent($emailComponent, 'Uses');
$securityComponent->usesContainer($database, 'Reads from and writes to', 'JDBC');
$mainframeBankingSystemFacade->usesSoftwareSystem($mainframeBankingSystem, 'Uses', 'XML/HTTPS');
$emailComponent->usesSoftwareSystem($emailSystem, 'Sends e-mail using');

$model->addImplicitRelationships();

// deployment nodes and container instances
$developerLaptop = $model->addDeploymentNode('Developer Laptop', 'Development', 'A developer laptop.', 'Microsoft Windows 10 or Apple macOS');
$apacheTomcat = $developerLaptop->addDeploymentNode('Docker Container - Web Server', 'Development', 'A Docker container.', 'Docker')
    ->addDeploymentNode(
        'Apache Tomcat',
        'Development',
        'An open source Java EE web server.',
        'Apache Tomcat 8.x',
        1,
        new Properties(new Property('Xmx', '512M'), new Property('Xms', '1024M'), new Property('Java Version', '8'))
    );
$apacheTomcat->add($webApplication);
$apacheTomcat->add($apiApplication);

$developerLaptop->addDeploymentNode('Docker Container - Database Server', 'Development', 'A Docker container.', 'Docker')
    ->addDeploymentNode('Database Server', 'Development', 'A development database.', 'Oracle 12c')
    ->add($database);

$developerLaptop->addDeploymentNode('Web Browser', 'Development', '', 'Google Chrome, Mozilla Firefox, Apple Safari or Microsoft Edge')
    ->add($singlePageApplication);

$customerMobileDevice = $model->addDeploymentNode("Customer's mobile device", 'Live', '', 'Apple iOS or Android');
$customerMobileDevice->add($mobileApp);

$customerComputer = $model->addDeploymentNode("Customer's computer", 'Live', '', 'Microsoft Windows or Apple macOS');
$customerComputer->addDeploymentNode('Web Browser', 'Live', '', 'Google Chrome, Mozilla Firefox, Apple Safari or Microsoft Edge')
    ->add($singlePageApplication);

$bigBankDataCenter = $model->addDeploymentNode('Big Bank plc', 'Live', '', 'Big Bank plc data center');

$liveWebServer = $bigBankDataCenter->addDeploymentNode('bigbank-web***', 'Live', 'A web server residing in the web server farm, accessed via F5 BIG-IP LTMs.', 'Ubuntu 16.04 LTS', 4, new Properties(new Property('Location', 'London and Reading')));
$liveWebServer->addDeploymentNode('Apache Tomcat', 'Live', 'An open source Java EE web server.', 'Apache Tomcat 8.x', 1, new Properties(new Property('Xmx', '512M'), new Property('Xms', '1024M'), new Property('Java Version', '8')))
    ->add($webApplication);

$liveApiServer = $bigBankDataCenter->addDeploymentNode('bigbank-api***', 'Live', 'A web server residing in the web server farm, accessed via F5 BIG-IP LTMs.', 'Ubuntu 16.04 LTS', 8, new Properties(new Property('Location', 'London and Reading')));
$liveApiServer->addDeploymentNode('Apache Tomcat', 'Live', 'An open source Java EE web server.', 'Apache Tomcat 8.x', 1, new Properties(new Property('Xmx', ' 512M'), new Property('Xms', '1024M'), new Property('Java Version', '8')))
    ->add($apiApplication);

$primaryDatabaseServer = $bigBankDataCenter->addDeploymentNode('bigbank-db01', 'Live', 'The primary database server.', 'Ubuntu 16.04 LTS', 1, new Properties(new Property('Location', 'London')))
    ->addDeploymentNode('Oracle - Primary', 'Live', 'The primary, live database server.', 'Oracle 12c');
$primaryDatabaseServer->add($database);

$secondaryDatabaseServer = $bigBankDataCenter->addDeploymentNode('bigbank-db02', 'Live', 'The secondary database server.', 'Ubuntu 16.04 LTS', 1, new Properties(new Property('Location', 'Reading')))
    ->addDeploymentNode('Oracle - Secondary', 'Live', 'A secondary, standby database server, used for failover purposes only.', 'Oracle 12c');
$secondaryDatabase = $secondaryDatabaseServer->add($database);

$relationships = \array_filter(
    $model->getRelationships(),
    function (Relationship $relationship) use ($secondaryDatabase) {
        return $relationship->getDestination()->equals($secondaryDatabase);
    }
);

/** @var Relationship $relationship */
foreach ($relationships as $relationship) {
    $relationship->addTags(FAILOVER_TAG);
}

$dataReplicationRelationship = $primaryDatabaseServer->usesDeploymentNode($secondaryDatabaseServer, 'Replicates data to', '');
$secondaryDatabase->addTags(FAILOVER_TAG);

// views/diagrams
$systemLandscapeView = $views->createSystemLandscapeView('SystemLandscape', 'The system landscape diagram for Big Bank plc.');
$systemLandscapeView->addAllElements();
$systemLandscapeView->setPaperSize(PaperSize::A5_Landscape());

$systemContextView = $views->createSystemContextView($internetBankingSystem, 'SystemContext', 'The system context diagram for the Internet Banking System.');
$systemContextView->setEnterpriseBoundaryVisible(false);
$systemContextView->addNearestNeighbours($internetBankingSystem);
$systemContextView->setPaperSize(PaperSize::A5_Landscape());

$containerView = $views->createContainerView($internetBankingSystem, 'Containers', 'The container diagram for the Internet Banking System.');
$containerView->addPerson($customer);
$containerView->addAllContainers();
$containerView->addSoftwareSystem($mainframeBankingSystem);
$containerView->addSoftwareSystem($emailSystem);
$containerView->setPaperSize(PaperSize::A5_Landscape());

$componentView = $views->createComponentView($apiApplication, 'Components', 'The component diagram for the API Application.');
$componentView->addContainer($mobileApp);
$componentView->addContainer($singlePageApplication);
$componentView->addContainer($database);
$componentView->addAllComponents();
$componentView->addSoftwareSystem($mainframeBankingSystem);
$componentView->addSoftwareSystem($emailSystem);
$componentView->setPaperSize(PaperSize::A5_Landscape());

//$systemLandscapeView->addAnimation($internetBankingSystem, $customer, $mainframeBankingSystem, $emailSystem);
//$systemLandscapeView->addAnimation($atm);
//$systemLandscapeView->addAnimation($customerServiceStaff, $backOfficeStaff);

//$systemContextView->addAnimation($internetBankingSystem);
//$systemContextView->addAnimation($customer);
//$systemContextView->addAnimation($mainframeBankingSystem);
//$systemContextView->addAnimation($emailSystem);
//
//$containerView->addAnimation($customer, $mainframeBankingSystem, $emailSystem);
//$containerView->addAnimation($webApplication);
//$containerView->addAnimation($singlePageApplication);
//$containerView->addAnimation($mobileApp);
//$containerView->addAnimation($apiApplication);
//$containerView->addAnimation($database);
//
//$componentView->addAnimation($singlePageApplication, $mobileApp, $database, $emailSystem, $mainframeBankingSystem);
//$componentView->addAnimation($signinController, $securityComponent);
//$componentView->addAnimation($accountsSummaryController, $mainframeBankingSystemFacade);
//$componentView->addAnimation($resetPasswordController, $emailComponent);

// dynamic diagrams and deployment diagrams are not available with the Free Plan
$dynamicView = $views->createContainerDynamicView($apiApplication, 'SignIn', 'Summarises how the sign in feature works in the single-page application.');
$dynamicView->add($singlePageApplication, 'Submits credentials to', $signinController);
$dynamicView->add($signinController, 'Calls isAuthenticated() on', $securityComponent);
$dynamicView->add($securityComponent, 'select * from users where username = ?', $database);
$dynamicView->setPaperSize(PaperSize::A5_Landscape());

$developmentDeploymentView = $views->createDeploymentView($internetBankingSystem, 'DevelopmentDeployment', 'An example development deployment scenario for the Internet Banking System.');
$developmentDeploymentView->setEnvironment('Development');
$developmentDeploymentView->add($developerLaptop);
$developmentDeploymentView->setPaperSize(PaperSize::A5_Landscape());

$liveDeploymentView = $views->createDeploymentView($internetBankingSystem, 'LiveDeployment', 'An example live deployment scenario for the Internet Banking System.');
$liveDeploymentView->setEnvironment('Live');
$liveDeploymentView->add($bigBankDataCenter);
$liveDeploymentView->add($customerMobileDevice);
$liveDeploymentView->add($customerComputer);
$liveDeploymentView->addRelationship($dataReplicationRelationship);
$liveDeploymentView->setPaperSize(PaperSize::A5_Landscape());

$styles = $views->getConfiguration()->getStyles();
$styles->addElementStyle(Tags::ELEMENT)->color('#ffffff');
$styles->addElementStyle(Tags::SOFTWARE_SYSTEM)->background('#1168bd');
$styles->addElementStyle(Tags::CONTAINER)->background('#438dd5');
$styles->addElementStyle(Tags::COMPONENT)->background('#85bbf0')->color('#000000');
$styles->addElementStyle(Tags::PERSON)->background('#08427b')->shape(Shape::Person())->fontSize(22);
$styles->addElementStyle(EXISTING_SYSTEM_TAG)->background('#999999');
$styles->addElementStyle(BANK_STAFF_TAG)->background('#999999');
$styles->addElementStyle(WEB_BROWSER_TAG)->shape(Shape::WebBrowser());
$styles->addElementStyle(MOBILE_APP_TAG)->shape(Shape::MobileDeviceLandscape());
$styles->addElementStyle(DATABASE_TAG)->shape(Shape::Cylinder());
$styles->addElementStyle(FAILOVER_TAG)->opacity(25);
$styles->addRelationshipStyle(FAILOVER_TAG)->opacity(25)->position(70);

$template = new StructurizrDocumentationTemplate($workspace);
        $template->addContextSection(
            $internetBankingSystem,
            Format::markdown(),
            "Here is some context about the Internet Banking System...\n" .
            "![](embed:SystemLandscape)\n" .
            "![](embed:SystemContext)\n" .
            "### Internet Banking System\n...\n" .
            "### Mainframe Banking System\n...\n"
        );
        $template->addContainersSection(
            $internetBankingSystem,
            Format::markdown(),
            "Here is some information about the containers within the Internet Banking System...\n" .
            "![](embed:Containers)\n" .
            "### Web Application\n...\n" .
            "### Database\n...\n"
        );
        $template->addComponentsSection(
            $webApplication,
            Format::markdown(),
            "Here is some information about the API Application...\n" .
            "![](embed:Components)\n" .
            "### Sign in process\n" .
            "Here is some information about the Sign In Controller, including how the sign in process works...\n" .
            '![](embed:SignIn)'
        );
        $template->addDevelopmentEnvironmentSection(
            $internetBankingSystem,
            Format::markdown(),
            "Here is some information about how to set up a development environment for the Internet Banking System...\n" .
            'image::embed:DevelopmentDeployment[]'
        );
        $template->addDeploymentSection(
            $internetBankingSystem,
            Format::markdown(),
            "Here is some information about the live deployment environment for the Internet Banking System...\n" .
            'image::embed:LiveDeployment[]'
        );

$client = new Client(
    new Credentials((string) \getenv('STRUCTURIZR_API_KEY'), (string) \getenv('STRUCTURIZR_API_SECRET')),
    new UrlMap('https://api.structurizr.com'),
    new Psr18Client(),
    new SymfonyRequestFactory(),
    // Logger can be replaced with new NullLogger()
    (new Logger('structurizr'))->pushHandler(new StreamHandler(__DIR__ . '/var/logs/' . \basename(__FILE__) . '.log', Logger::DEBUG))
);
$client->put($workspace);
