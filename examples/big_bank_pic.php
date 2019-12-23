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
use StructurizrPHP\Core\Model\Enterprise;
use StructurizrPHP\Core\Model\Location;
use StructurizrPHP\Core\Model\Properties;
use StructurizrPHP\Core\Model\Property;
use StructurizrPHP\Core\View\PaperSize;
use StructurizrPHP\Core\Workspace;
use Symfony\Component\HttpClient\Psr18Client;

require __DIR__ . '/../vendor/autoload.php';

const EXISTING_SYSTEM_TAG = "Existing System";
const BANK_STAFF_TAG = "Bank Staff";
const WEB_BROWSER_TAG = "Web Browser";
const MOBILE_APP_TAG = "Mobile App";
const DATABASE_TAG = "Database";
const FAILOVER_TAG = "Failover";

$workspace = new Workspace((string)\getenv('STRUCTURIZR_WORKSPACE_ID'), "Big Bank plc", "This is an example workspace to illustrate the key features of Structurizr, based around a fictional online banking system.");
$model = $workspace->getModel();
$views = $workspace->getViews();

$model->setEnterprise(new Enterprise("Big Bank plc"));

// people and software systems
$customer = $model->addPerson("Personal Banking Customer", "A customer of the bank, with personal bank accounts.", Location::external());

$internetBankingSystem = $model->addSoftwareSystem("Internet Banking System", "Allows customers to view information about their bank accounts, and make payments.", Location::internal());
$customer->uses($internetBankingSystem, "Views account balances, and makes payments using");

$mainframeBankingSystem = $model->addSoftwareSystem("Mainframe Banking System", "Stores all of the core banking information about customers, accounts, transactions, etc.", Location::internal());
$mainframeBankingSystem->addTags(EXISTING_SYSTEM_TAG);
$internetBankingSystem->usesSoftwareSystem($mainframeBankingSystem, "Gets account information from, and makes payments using");

$emailSystem = $model->addSoftwareSystem("E-mail System", "The internal Microsoft Exchange e-mail system.", Location::internal());
$internetBankingSystem->usesSoftwareSystem($emailSystem, "Sends e-mail using");
$emailSystem->addTags(EXISTING_SYSTEM_TAG);
$emailSystem->delivers($customer, "Sends e-mails to");

$atm = $model->addSoftwareSystem("ATM", "Allows customers to withdraw cash.", Location::internal());
$atm->addTags(EXISTING_SYSTEM_TAG);
$atm->usesSoftwareSystem($mainframeBankingSystem, "Uses");
$customer->uses($atm, "Withdraws cash using");

$customerServiceStaff = $model->addPerson("Customer Service Staff", "Customer service staff within the bank.", Location::internal());
$customerServiceStaff->addTags(BANK_STAFF_TAG);
$customerServiceStaff->uses($mainframeBankingSystem, "Uses");
$customer->interactsWith($customerServiceStaff, "Asks questions to", "Telephone");

$backOfficeStaff = $model->addPerson("Back Office Staff", "Administration and support staff within the bank.", Location::internal());
$backOfficeStaff->addTags(BANK_STAFF_TAG);
$backOfficeStaff->uses($mainframeBankingSystem, "Uses");

// containers
$singlePageApplication = $internetBankingSystem->addContainer("Single-Page Application", "Provides all of the Internet banking functionality to customers via their web browser.", "JavaScript and Angular");
$singlePageApplication->addTags(WEB_BROWSER_TAG);
$mobileApp = $internetBankingSystem->addContainer("Mobile App", "Provides a limited subset of the Internet banking functionality to customers via their mobile device.", "Xamarin");
$mobileApp->addTags(MOBILE_APP_TAG);
$webApplication = $internetBankingSystem->addContainer("Web Application", "Delivers the static content and the Internet banking single page application.", "Java and Spring MVC");
$apiApplication = $internetBankingSystem->addContainer("API Application", "Provides Internet banking functionality via a JSON/HTTPS API.", "Java and Spring MVC");
$database = $internetBankingSystem->addContainer("Database", "Stores user registration information, hashed authentication credentials, access logs, etc.", "Oracle Database Schema");
$database->addTags(DATABASE_TAG);

$customer->uses($webApplication, "Visits bigbank.com/ib using", "HTTPS");
$customer->uses($singlePageApplication, "Views account balances, and makes payments using");
$customer->uses($mobileApp, "Views account balances, and makes payments using");
$webApplication->uses($singlePageApplication, "Delivers to the customer's web browser");
$apiApplication->uses($database, "Reads from and writes to", "JDBC");
$apiApplication->usesSoftwareSystem($mainframeBankingSystem, "Makes API calls to", "XML/HTTPS");
$apiApplication->usesSoftwareSystem($emailSystem, "Sends e-mail using", "SMTP");

// components
// - for a real-world software system, you would probably want to extract the components using
// - static analysis/reflection rather than manually specifying them all
$signinController = $apiApplication->addComponent("Sign In Controller", "Allows users to sign in to the Internet Banking System.", "Spring MVC Rest Controller");
$accountsSummaryController = $apiApplication->addComponent("Accounts Summary Controller", "Provides customers with a summary of their bank accounts.", "Spring MVC Rest Controller");
$resetPasswordController = $apiApplication->addComponent("Reset Password Controller", "Allows users to reset their passwords with a single use URL.", "Spring MVC Rest Controller");
$securityComponent = $apiApplication->addComponent("Security Component", "Provides functionality related to signing in, changing passwords, etc.", "Spring Bean");
$mainframeBankingSystemFacade = $apiApplication->addComponent("Mainframe Banking System Facade", "A facade onto the mainframe banking system.", "Spring Bean");
$emailComponent = $apiApplication->addComponent("E-mail Component", "Sends e-mails to users.", "Spring Bean");

$signinController->usesComponent($securityComponent, "Uses");
$accountsSummaryController->usesComponent($mainframeBankingSystemFacade, "Uses");
$resetPasswordController->usesComponent($securityComponent, "Uses");
$resetPasswordController->usesComponent($emailComponent, "Uses");
$securityComponent->usesContainer($database, "Reads from and writes to", "JDBC");
$mainframeBankingSystemFacade->usesSoftwareSystem($mainframeBankingSystem, "Uses", "XML/HTTPS");
$emailComponent->usesSoftwareSystem($emailSystem, "Sends e-mail using");

$model->addImplicitRelationships();

// deployment nodes and container instances
$developerLaptop = $model->addDeploymentNode("Developer Laptop", "Development", "A developer laptop.", "Microsoft Windows 10 or Apple macOS");
$apacheTomcat = $developerLaptop->addDeploymentNode("Docker Container - Web Server", "Development", "A Docker container.", "Docker")
    ->addDeploymentNode(
        "Apache Tomcat",
        "Development",
        "An open source Java EE web server.",
        "Apache Tomcat 8.x",
        1,
        new Properties(new Property("Xmx", "512M"), new Property("Xms", "1024M"), new Property("Java Version", "8"))
    );
$apacheTomcat->add($webApplication);
$apacheTomcat->add($apiApplication);

$developerLaptop->addDeploymentNode("Docker Container - Database Server", "Development", "A Docker container.", "Docker")
    ->addDeploymentNode("Database Server", "Development", "A development database.", "Oracle 12c")
    ->add($database);

$developerLaptop->addDeploymentNode("Web Browser", "Development", "", "Google Chrome, Mozilla Firefox, Apple Safari or Microsoft Edge")
    ->add($singlePageApplication);

$customerMobileDevice = $model->addDeploymentNode("Customer's mobile device", "Live", "", "Apple iOS or Android");
$customerMobileDevice->add($mobileApp);

$customerComputer = $model->addDeploymentNode("Customer's computer", "Live", "", "Microsoft Windows or Apple macOS");
$customerComputer->addDeploymentNode("Web Browser", "Live", "", "Google Chrome, Mozilla Firefox, Apple Safari or Microsoft Edge")
    ->add($singlePageApplication);

$bigBankDataCenter = $model->addDeploymentNode("Big Bank plc", "Live", "", "Big Bank plc data center");

$liveWebServer = $bigBankDataCenter->addDeploymentNode("bigbank-web***", "Live", "A web server residing in the web server farm, accessed via F5 BIG-IP LTMs.", "Ubuntu 16.04 LTS", 4, new Properties(new Property("Location", "London and Reading")));
$liveWebServer->addDeploymentNode("Apache Tomcat", "Live", "An open source Java EE web server.", "Apache Tomcat 8.x", 1, new Properties(new Property("Xmx", "512M"), new Property("Xms", "1024M"), new Property("Java Version", "8")))
    ->add($webApplication);

$liveApiServer = $bigBankDataCenter->addDeploymentNode("bigbank-api***", "Live", "A web server residing in the web server farm, accessed via F5 BIG-IP LTMs.", "Ubuntu 16.04 LTS", 8, new Properties(new Property("Location", "London and Reading")));
$liveApiServer->addDeploymentNode("Apache Tomcat", "Live", "An open source Java EE web server.", "Apache Tomcat 8.x", 1, new Properties(new Property("Xmx", " 512M"), new Property("Xms", "1024M"), new Property("Java Version", "8")))
    ->add($apiApplication);

$primaryDatabaseServer = $bigBankDataCenter->addDeploymentNode("bigbank-db01", "Live", "The primary database server.", "Ubuntu 16.04 LTS", 1, new Properties(new Property("Location", "London")))
    ->addDeploymentNode("Oracle - Primary", "Live", "The primary, live database server.", "Oracle 12c");
$primaryDatabaseServer->add($database);

$secondaryDatabaseServer = $bigBankDataCenter->addDeploymentNode("bigbank-db02", "Live", "The secondary database server.", "Ubuntu 16.04 LTS", 1, new Properties(new Property("Location", "Reading")))
    ->addDeploymentNode("Oracle - Secondary", "Live", "A secondary, standby database server, used for failover purposes only.", "Oracle 12c");
$secondaryDatabase = $secondaryDatabaseServer->add($database);

//model.getRelationships().stream().filter(r -> r.getDestination().equals(secondaryDatabase)).forEach(r -> r.addTags(FAILOVER_TAG));
$dataReplicationRelationship = $primaryDatabaseServer->usesDeploymentNode($secondaryDatabaseServer, "Replicates data to", "");
$secondaryDatabase->addTags(FAILOVER_TAG);

// views/diagrams
$systemLandscapeView = $views->createSystemLandscapeView("SystemLandscape", "The system landscape diagram for Big Bank plc.");
$systemLandscapeView->addAllElements();
$systemLandscapeView->setPaperSize(PaperSize::A5_Landscape());

$systemContextView = $views->createSystemContextView($internetBankingSystem, "SystemContext", "The system context diagram for the Internet Banking System.");
//$systemContextView->setEnterpriseBoundaryVisible(false);
//$systemContextView->addNearestNeighbours($internetBankingSystem);
$systemContextView->setPaperSize(PaperSize::A5_Landscape());

$containerView = $views->createContainerView($internetBankingSystem, "Containers", "The container diagram for the Internet Banking System.");
$containerView->addPerson($customer);
$containerView->addAllContainers();
$containerView->addSoftwareSystem($mainframeBankingSystem);
$containerView->addSoftwareSystem($emailSystem);
$containerView->setPaperSize(PaperSize::A5_Landscape());

$componentView = $views->createComponentView($apiApplication, "Components", "The component diagram for the API Application.");
$componentView->addContainer($mobileApp);
$componentView->addContainer($singlePageApplication);
$componentView->addContainer($database);
$componentView->addAllComponents();
$componentView->addSoftwareSystem($mainframeBankingSystem);
$componentView->addSoftwareSystem($emailSystem);
$componentView->setPaperSize(PaperSize::A5_Landscape());

$client = new Client(
    new Credentials((string)\getenv('STRUCTURIZR_API_KEY'), (string)\getenv('STRUCTURIZR_API_SECRET')),
    new UrlMap('https://api.structurizr.com'),
    new Psr18Client(),
    new SymfonyRequestFactory(),
    // Logger can be replaced with new NullLogger()
    (new Logger('structurizr'))->pushHandler(new StreamHandler(__DIR__ . '/var/logs/' . basename(__FILE__) . '.log', Logger::DEBUG))
);
$client->put($workspace);
