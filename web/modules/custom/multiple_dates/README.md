CONTENTS OF THIS FILE
---------------------
 * Introduction
 * Requirements
 * Installation
 * Configuration
 * Troubleshooting
 * Author/Maintainers

INTRODUCTION
-----------

 * Multiple Dates module provides a custom field for multiple dates and the widget which provides form element using the jQuery UI datetimepicker. This module that enables jQuery UI calendar to manage multiple dates with the following features:

	** Select date ranges.
	** Pick multiple dates not in secuence.
	** Define a maximum number of pickable dates.
	** Define a range X days from where it is possible to select Y dates.
	** Define unavailable dates.

REQUIREMENTS
------------

 * jquery_ui_datepicker

INSTALLATION
------------

 * To install, copy the Multiple Dates directory and
   all its contents to your modules directory.

 * To enable the module go to Administer > Modules, and enable
   "Multiple Dates".

CONFIGURATION
-------------
 * Create a field for multiple dates. Manage fields > Add a new field > Multiple Dates

 * Create a widget for Multiple Dates field in a content type.

 * Edit the Content type and navigate to "Manage form display" tab.

 * Under "Widget" select widget for Multiple Dates for "Multiple Dates".

	** Date select type
	** Set maximum picks
	** Days range
	** Disable specific dates from calendar
	** Min date
	** Max date
	** Disable specific days in week
	** Number Of Months

 * Edit the Content type and navigate to "Manage display" tab.

 * Under "FORMAT" select widget for Multiple Dates for "Multiple Dates".

	** Months display layout
	** Date format
	** Number of months