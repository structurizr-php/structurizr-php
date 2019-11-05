# Structurizr for PHP

This repository is a port of [Structirizr for Java](https://github.com/structurizr/java).
All credits for creating [C4](https://c4model.com/) goes of course to [Simon Brown](https://github.com/simonbrowndotje)
this library is nothing more that simple port of the code that already exists in other language.  

# How to Use 

> Careful, this port is still under heavy development, API might change over time, it's not recommended for production
> usage yet. 

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
);
$client->put($workspace);
```

The view can then be exported to be visualised in a number of different ways; e.g. PlantUML, Structurizr and Graphviz:

![Views can be exported and visualised in many ways; e.g. PlantUML, Structurizr and Graphviz](/docs/images/getting-started.png)