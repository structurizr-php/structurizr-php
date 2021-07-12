## [Unreleased] - 2021-07-12

### Added
- [#99](https://github.com/structurizr-php/structurizr-php/pull/99) - **Allow PHP 8.0** - [@tomaszhanc](https://github.com/tomaszhanc)

## [0.3.0] - 2021-04-18

### Added
- [#78](https://github.com/structurizr-php/structurizr-php/pull/78) - **Added constants for Location types** - [@alleknalle](https://github.com/alleknalle)
- [#78](https://github.com/structurizr-php/structurizr-php/pull/78) - **Added getLocation() to SoftwareSystem** - [@alleknalle](https://github.com/alleknalle)
- [#78](https://github.com/structurizr-php/structurizr-php/pull/78) - **Added functionality to only add a single ContainerInstance/InfrastructureNode and it's parents to a DeploymentView** - [@alleknalle](https://github.com/alleknalle)
- [#76](https://github.com/structurizr-php/structurizr-php/pull/76) - **Added more supported paper sizes for views** - [@alleknalle](https://github.com/alleknalle)
- [#75](https://github.com/structurizr-php/structurizr-php/pull/75) - **Added possibility to get Elements from Model by Tag** - [@alleknalle](https://github.com/alleknalle)
- [#75](https://github.com/structurizr-php/structurizr-php/pull/75) - **Added (undocumented) tag for response relationships** - [@alleknalle](https://github.com/alleknalle)
- [#72](https://github.com/structurizr-php/structurizr-php/pull/72) - **Added support for externalContainerBoundariesVisible for ComponentView** - [@alleknalle](https://github.com/alleknalle)
- [#72](https://github.com/structurizr-php/structurizr-php/pull/72) - **Added addAllNearestNeighbours() for ComponentView to automatically add all nearest neighbours to view** - [@alleknalle](https://github.com/alleknalle)
- [#72](https://github.com/structurizr-php/structurizr-php/pull/72) - **Added addAllNearestNeighbours() for ContainerView to automatically add all nearest neighbours to view** - [@alleknalle](https://github.com/alleknalle)
- [#72](https://github.com/structurizr-php/structurizr-php/pull/72) - **Added support for response relationships in DynamicView** - [@alleknalle](https://github.com/alleknalle)
- [#72](https://github.com/structurizr-php/structurizr-php/pull/72) - **Added getters for basic View data** - [@alleknalle](https://github.com/alleknalle)

### Changed
- [#78](https://github.com/structurizr-php/structurizr-php/pull/78) - **Moved parent from ContainerInstance/DeploymentNode to DeploymentElement** - [@alleknalle](https://github.com/alleknalle)
- [#78](https://github.com/structurizr-php/structurizr-php/pull/78) - **Moved usesDeploymentElement() from InfrastructureNode to DeploymentElement** - [@alleknalle](https://github.com/alleknalle)
- [#78](https://github.com/structurizr-php/structurizr-php/pull/78) - **Fixes for parent in all deployment layers to be compatible changes in parent** - [@alleknalle](https://github.com/alleknalle)
- [#72](https://github.com/structurizr-php/structurizr-php/pull/72) - **Only add Elements and Relationships to View if they don't already exist to keep the JSON clean** - [@alleknalle](https://github.com/alleknalle)

### Fixed
- [#77](https://github.com/structurizr-php/structurizr-php/pull/77) - **When adding a child deployment node from model, it was not added to the children of the parent** - [@alleknalle](https://github.com/alleknalle)
- [#75](https://github.com/structurizr-php/structurizr-php/pull/75) - **Corrected order of parameters in constructor for all Views** - [@alleknalle](https://github.com/alleknalle)
- [#74](https://github.com/structurizr-php/structurizr-php/pull/74) - **External boundaries in ComponentView was not working as expected** - [@alleknalle](https://github.com/alleknalle)

### Removed
- [#78](https://github.com/structurizr-php/structurizr-php/pull/78) - **Removed getParent() from Element, since Element doesn't have a parent** - [@alleknalle](https://github.com/alleknalle)
- [#78](https://github.com/structurizr-php/structurizr-php/pull/78) - **Removed usesInfrastructureNode() from StaticStructureElementInstance since usesDeploymentElement() is part of it and can be used instead** - [@alleknalle](https://github.com/alleknalle)

## [0.2.0] - 2021-04-04

### Added
- [#70](https://github.com/structurizr-php/structurizr-php/pull/70) - **Added support for Infrastructure Node. Based on JAVA SDK: https://github.com/structurizr/java/blob/master/structurizr-core/src/com/structurizr/model/InfrastructureNode.java** - [@alleknalle](https://github.com/alleknalle)
- [#70](https://github.com/structurizr-php/structurizr-php/pull/70) - **Added StaticStructureElementInstance for future support for SoftwareSystemInstance** - [@alleknalle](https://github.com/alleknalle)
- [#70](https://github.com/structurizr-php/structurizr-php/pull/70) - **Added support for showing external boundaries in DeploymentViews. Based on JAVA SDK: https://github.com/structurizr/java/commit/d21943609be36d5e34e89c67fb74c4ab16f143fe** - [@alleknalle](https://github.com/alleknalle)

### Changed
- [#48](https://github.com/structurizr-php/structurizr-php/pull/48) - **Update RelationshipStyle.php** - [@smalot](https://github.com/smalot)
- [#49](https://github.com/structurizr-php/structurizr-php/pull/49) - **Update Tags.php** - [@smalot](https://github.com/smalot)
- [7c29b5](https://github.com/structurizr-php/structurizr-php/commit/7c29b5f31b1f64a01a44118807b3a4a52b881cf8) - **Adde integration with aeon-php/automation** - [@norberttech](https://github.com/norberttech)
- [#50](https://github.com/structurizr-php/structurizr-php/pull/50) - **github actions** - [@norberttech](https://github.com/norberttech)
- [#43](https://github.com/structurizr-php/structurizr-php/pull/43) - **Avoid exception on missing parameters** - [@smalot](https://github.com/smalot)
- [bbfd3c](https://github.com/structurizr-php/structurizr-php/commit/bbfd3c372055ebc9f6f7d5e4cd6efba458bd616c) - **Update CHANGELOG.md** - [@norberttech](https://github.com/norberttech)

### Fixed
- [#70](https://github.com/structurizr-php/structurizr-php/pull/70) - **Fixed typo for DEFAULT_DEPLOYMENT_ENVIRONMENT in DeploymentElement** - [@alleknalle](https://github.com/alleknalle)
- [#62](https://github.com/structurizr-php/structurizr-php/pull/62) - **Fix ViewSet calling ContainerView::__construct incorrectly.** - [@tdgroot](https://github.com/tdgroot)
- [#41](https://github.com/structurizr-php/structurizr-php/pull/41) - **a typo** - [@marclaporte](https://github.com/marclaporte)
- [#40](https://github.com/structurizr-php/structurizr-php/pull/40) - **extensions-php composer.json dependency name** - [@thunderer](https://github.com/thunderer)
- [#38](https://github.com/structurizr-php/structurizr-php/pull/38) - **link to Hire in Social portal in README** - [@ZielinskiLukasz](https://github.com/ZielinskiLukasz)

### Removed
- [#67](https://github.com/structurizr-php/structurizr-php/pull/67) - **Removes the restrictions related to adding containers/components outside the scoped software system/container** - [@alleknalle](https://github.com/alleknalle)
- [4c479a](https://github.com/structurizr-php/structurizr-php/commit/4c479ae8009ca66130bd7fc7aa85ea950737fa73) - **references to master branch** - [@norberttech](https://github.com/norberttech)

## [0.1.0] - 2020-04-22

### Added
- [#35](https://github.com/structurizr-php/structurizr-php/pull/35) - **missing pieces to big bank plc examples** - [@norberttech](https://github.com/norberttech)
- [#34](https://github.com/structurizr-php/structurizr-php/pull/34) - **missing NearestNeigbhours method to StaticView** - [@norberttech](https://github.com/norberttech)
- [#33](https://github.com/structurizr-php/structurizr-php/pull/33) - **ComponentView** - [@norberttech](https://github.com/norberttech)
- [#24](https://github.com/structurizr-php/structurizr-php/pull/24) - **delivers method to StaticStructureElement object API** - [@norberttech](https://github.com/norberttech)
- [#21](https://github.com/structurizr-php/structurizr-php/pull/21) - **missing methods to Container and Static view** - [@norberttech](https://github.com/norberttech)
- [#17](https://github.com/structurizr-php/structurizr-php/pull/17) - **deptrac to keep boundaries between namespaces** - [@norberttech](https://github.com/norberttech)
- [#16](https://github.com/structurizr-php/structurizr-php/pull/16) - **psr/logger to http client** - [@norberttech](https://github.com/norberttech)
- [513838](https://github.com/structurizr-php/structurizr-php/commit/513838689e04ef35fb29c1aa8de071e7b7500168) - **reference to PHP ADR tool** - [@norberttech](https://github.com/norberttech)
- [#14](https://github.com/structurizr-php/structurizr-php/pull/14) - **AdrTools** - [@jwojtyra](https://github.com/jwojtyra)
- [#12](https://github.com/structurizr-php/structurizr-php/pull/12) - **more phpstan & cs fixer rules** - [@norberttech](https://github.com/norberttech)
- [#11](https://github.com/structurizr-php/structurizr-php/pull/11) - **phpstan rule to detect missing return types** - [@norberttech](https://github.com/norberttech)
- [#10](https://github.com/structurizr-php/structurizr-php/pull/10) - **phpstan integration** - [@norberttech](https://github.com/norberttech)
- [#7](https://github.com/structurizr-php/structurizr-php/pull/7) - **DeploymentNodes and DeploymentViews** - [@norberttech](https://github.com/norberttech)
- [#6](https://github.com/structurizr-php/structurizr-php/pull/6) - **support for Container model and Dynamic Views** - [@norberttech](https://github.com/norberttech)

### Changed
- [#37](https://github.com/structurizr-php/structurizr-php/pull/37) - **Prepare first release** - [@akondas](https://github.com/akondas)
- [#36](https://github.com/structurizr-php/structurizr-php/pull/36) - **Implement removeElement in View** - [@akondas](https://github.com/akondas)
- [#28](https://github.com/structurizr-php/structurizr-php/pull/28) - **relationshipstyle class & unit tests** - [@macieyb](https://github.com/macieyb)
- [1b8d0e](https://github.com/structurizr-php/structurizr-php/commit/1b8d0e6daf4b08d4f4df5eaafb2736eb6828b867) - **Update README.md** - [@norberttech](https://github.com/norberttech)
- [49cd05](https://github.com/structurizr-php/structurizr-php/commit/49cd0523822e97743989a1511a9b835dc5957e9f) - **Update composer.json** - [@norberttech](https://github.com/norberttech)
- [#27](https://github.com/structurizr-php/structurizr-php/pull/27) - **Enhancement: Use ergebnis/phpstan-rules instead of localheinz/phpstan-rules** - [@localheinz](https://github.com/localheinz)
- [#26](https://github.com/structurizr-php/structurizr-php/pull/26) - **Extracted extensions to standalone repository & improved CI testsuite** - [@norberttech](https://github.com/norberttech)
- [#18](https://github.com/structurizr-php/structurizr-php/pull/18) - **Minor fixes** - [@norberttech](https://github.com/norberttech)
- [126fff](https://github.com/structurizr-php/structurizr-php/commit/126fffbeadd66996bcefe01e88d5db67753abe43) - **README.md** - [@norberttech](https://github.com/norberttech)
- [#13](https://github.com/structurizr-php/structurizr-php/pull/13) - **Health checks** - [@norberttech](https://github.com/norberttech)
- [#9](https://github.com/structurizr-php/structurizr-php/pull/9) - **Minor codding style fixes** - [@norberttech](https://github.com/norberttech)
- [#8](https://github.com/structurizr-php/structurizr-php/pull/8) - **Corporate branding** - [@jwojtyra](https://github.com/jwojtyra)
- [#5](https://github.com/structurizr-php/structurizr-php/pull/5) - **Made couple parameters of Person/Relationship/SoftwareSystem optional** - [@norberttech](https://github.com/norberttech)
- [#4](https://github.com/structurizr-php/structurizr-php/pull/4) - **Ported Core/Model/Configuration** - [@maniekcz](https://github.com/maniekcz)
- [#3](https://github.com/structurizr-php/structurizr-php/pull/3) - **Layout Merge Strategy & README improvements** - [@norberttech](https://github.com/norberttech)
- [#2](https://github.com/structurizr-php/structurizr-php/pull/2) - **Covered API get workspace endpoint** - [@norberttech](https://github.com/norberttech)
- [#1](https://github.com/structurizr-php/structurizr-php/pull/1) - **Ported "Shapes.java" example with all used in it features** - [@norberttech](https://github.com/norberttech)
- [f46fd6](https://github.com/structurizr-php/structurizr-php/commit/f46fd63aba469241fb752ddaa4da7948596f08b5) - **Initial commit** - [@norberttech](https://github.com/norberttech)

### Fixed
- [#31](https://github.com/structurizr-php/structurizr-php/pull/31) - **Component::toArray() method and removed useless dependency** - [@norberttech](https://github.com/norberttech)
- [#25](https://github.com/structurizr-php/structurizr-php/pull/25) - **for hydrating software system containers relationships** - [@norberttech](https://github.com/norberttech)
- [#23](https://github.com/structurizr-php/structurizr-php/pull/23) - **ContainerView layout merge strategy** - [@norberttech](https://github.com/norberttech)
- [#15](https://github.com/structurizr-php/structurizr-php/pull/15) - **invalid namespaces** - [@norberttech](https://github.com/norberttech)
- [c13fc2](https://github.com/structurizr-php/structurizr-php/commit/c13fc2ad585340cec65f794bcf4c552b549718b1) - **typo in composer.json** - [@norberttech](https://github.com/norberttech)
- [762ae7](https://github.com/structurizr-php/structurizr-php/commit/762ae7f11cabd0cdef9e8d8083cc812627cbde40) - **files header** - [@norberttech](https://github.com/norberttech)

### Removed
- [5994cc](https://github.com/structurizr-php/structurizr-php/commit/5994cca594fe3d8679e15891f0544dfeac37f0fe) - **development leftovers and updated DefaultLayoutMergeStrategy description** - [@norberttech](https://github.com/norberttech)
- [#32](https://github.com/structurizr-php/structurizr-php/pull/32) - **dependency that from early version of this library that is no longer used** - [@norberttech](https://github.com/norberttech)
- [#22](https://github.com/structurizr-php/structurizr-php/pull/22) - **greater or equal 0 assertion from ElementView x,y properties** - [@norberttech](https://github.com/norberttech)
- [ec77b7](https://github.com/structurizr-php/structurizr-php/commit/ec77b7c0a57b614f349c43eb4788bd92eb826b53) - **psalm leftover** - [@norberttech](https://github.com/norberttech)
- [f0255a](https://github.com/structurizr-php/structurizr-php/commit/f0255ac35cefba32b0e722df38798a78a3f19093) - **development leftovers** - [@norberttech](https://github.com/norberttech)

Generated by [Automation](https://github.com/aeon-php/automation)