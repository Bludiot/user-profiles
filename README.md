# User Profiles

Bludit CMS plugin that provides an author bio for posts, pages, and sidebar.

The plugin was designed to augment the Configure 8 theme but can be used with other themes. However, user profile pages will likely not wor without modifications to the theme which provide a page template specific to users. A user profile page does not instantiate the Page class to access it's methods (e.g. No cover image, no content).

![Tested on Bludit version 3.15.0](https://img.shields.io/badge/Bludit-3.15.0-42a5f5.svg?style=flat-square "Tested on Bludit version 3.15.0")
![Minimum PHP version is 7.4](https://img.shields.io/badge/PHP_Min-7.4-8892bf.svg?style=flat-square "Minimum PHP version is 7.4")
![Tested on PHP version 8.2.4](https://img.shields.io/badge/PHP_Test-8.2.4-8892bf.svg?style=flat-square "Tested on PHP version 8.2.4")

## Requirements

The active theme must have the `siteSidebar` hook for the author bio widget to display.
