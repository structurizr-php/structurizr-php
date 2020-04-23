# Structurizr for PHP

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.3-8892BF.svg)](https://php.net/)
[![Latest Stable Version](https://poser.pugx.org/structurizr-php/structurizr-php/version)](https://packagist.org/packages/structurizr-php/structurizr-php)
![](https://github.com/structurizr-php/structurizr-php/workflows/Tests/badge.svg?branch=master)
[![Total Downloads](https://poser.pugx.org/structurizr-php/structurizr-php/downloads)](https://packagist.org/packages/structurizr-php/structurizr-php)
![License](https://img.shields.io/github/license/structurizr-php/structurizr-php)

This repository is a port of [Structirizr for Java](https://github.com/structurizr/java).
All credits for creating [C4](https://c4model.com/) goes of course to [Simon Brown](https://github.com/simonbrowndotje)
this library is nothing more that simple port of the code that already exists in other language.  

# How to Use 

### Installation

```
composer require structurizr-php/structurizr-php
```

### A quick example

As an example, the following PHP code can be used to create a software architecture __model__ and an associated __view__ 
that describes a user using a software system.

```php
<?php 

$workspace = new Workspace(
    $id = (string)\getenv('STRUCTURIZR_WORKSPACE_ID'),
    $name = 'Getting Started',
    $description = 'This is a model of my software system. by structurizr-php/structurizr-php'
);
$workspace->getModel()->setEnterprise(new Enterprise('Structurizr PHP'));
$person = $workspace->getModel()->addPerson(
    $name = 'User',
    $description = 'A user of my software system.',
    Location::internal()
);
$softwareSystem = $workspace->getModel()->addSoftwareSystem(
    $name = 'Software System',
    $description = 'My software system.',
    Location::internal()
);
$person->usesSoftwareSystem($softwareSystem, 'Uses', 'Http');

$contextView = $workspace->getViews()->createSystemContextView($softwareSystem, 'System Context', 'system01', 'An example of a System Context diagram.');
$contextView->addAllElements();
$contextView->setAutomaticLayout(true);

$styles = $workspace->getViews()->getConfiguration()->getStyles();

$styles->addElementStyle(Tags::SOFTWARE_SYSTEM)->background("#1168bd")->color('#ffffff');
$styles->addElementStyle(Tags::PERSON)->background("#08427b")->color('#ffffff')->shape(Shape::person());

$client = new Client(
    new Credentials((string) \getenv('STRUCTURIZR_API_KEY'), (string) \getenv('STRUCTURIZR_API_SECRET')),
    new UrlMap('https://api.structurizr.com'),
    new Psr18Client(),
    new SymfonyRequestFactory(),
    new Logger('structurizr', [new StreamHandler('php://stdout')])
);
$client->put($workspace);
```

The view can then be exported to be visualised in a number of different ways; e.g. PlantUML, Structurizr and Graphviz:

![Views can be exported and visualised in many ways; e.g. PlantUML, Structurizr and Graphviz](/docs/images/getting-started.png)

## More Examples

### Hire in Social 

[Hire in Social - project example](https://github.com/itoffers-online/portal/blob/master/php/portal/structurizr/structurizr.php)

[Structurizr Workspace](https://structurizr.com/share/49192)

#### System Landscape

![System Landscape](https://structurizr.com/share/49192/images/system-landscape.png)
![System Landscape Key](https://structurizr.com/share/49192/images/system-landscape-key.png)

#### Container

![Container](https://structurizr.com/share/49192/images/Hire%20in%20Social%20-%20detailed%20view.png)
![Container Key](https://structurizr.com/share/49192/images/Hire%20in%20Social%20-%20detailed%20view-key.png)

### Big Bank Plc 

![System Landscape](/docs/images/big_bank_plc/SystemLandscape.png)
![System Context](/docs/images/big_bank_plc/SystemContext.png)
![Contianer](/docs/images/big_bank_plc/Container.png)
![Components](/docs/images/big_bank_plc/Components.png)
![Dynamic](/docs/images/big_bank_plc/Dynamic.png)
![Development Deployment](/docs/images/big_bank_plc/DevelopmentDeployment.png)
![Live Deployment](/docs/images/big_bank_plc/LiveDeployment.png)
