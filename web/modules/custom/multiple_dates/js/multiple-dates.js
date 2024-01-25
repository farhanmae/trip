/**
 * @file
 */

 (function ($, Drupal, drupalSettings) {
  'use strict';
  Drupal.behaviors.multiple_dates = {
    attach: function (context, settings) {
      if($('.multiple-dates-field').length > 0) {
        var date_format = drupalSettings.settings_data.date_format;
        var date_selection_type = drupalSettings.settings_data.date_selection_type;
        var maximum_picks_data = drupalSettings.settings_data.maximum_picks_data;
        var days_range_data = drupalSettings.settings_data.days_range_data;
        var exclude_date = drupalSettings.settings_data.exclude_date;
        var min_date = drupalSettings.settings_data.min_date;
        var max_date = drupalSettings.settings_data.max_date;
        var disable_days_week = drupalSettings.settings_data.disable_days_week;
        var change_year = drupalSettings.settings_data.change_year;
        var change_month = drupalSettings.settings_data.change_month;
        var number_of_months = drupalSettings.settings_data.number_of_months;
        const datesPickerData = {};
        datesPickerData.dateFormat = date_format;
        datesPickerData.changeMonth = change_month;
        datesPickerData.changeYear = change_year;
        var today = new Date();
        var y = today.getFullYear();

        if(number_of_months != '') {
          var numberOfMonthsArr = number_of_months.split(',');
          datesPickerData.numberOfMonths = numberOfMonthsArr;
        }

        if(date_selection_type == 'days_range') {
          datesPickerData.mode = 'daysRange';
          datesPickerData.autoselectRange = [0,days_range_data];
          // datesPickerData.beforeShowDay = $.datepicker.noWeekends;
        } else {
          // check if maximum_picks or days_range
          if(date_selection_type == 'maximum_picks') {
            datesPickerData.maxPicks = maximum_picks_data;
          }

          // check if min date is set
          if($.isNumeric(min_date)) {
            datesPickerData.minDate = min_date;
          }

          // check if max date is set
          if($.isNumeric(max_date)) {
            datesPickerData.maxDate = max_date;
          }

          // check if exclude_date not empty
          if(exclude_date != '') {
            var exclude_dates = [];
            var excludeDateArray = exclude_date.split(',');
          // add excluded dates
          jQuery.each(excludeDateArray, function(index, item) {
            if (Date.parse(item)) {
              const d = new Date(item);
              exclude_dates.push(d);
            }
          });
          datesPickerData.addDisabledDates = exclude_dates;
        }

          // disable days of week
          if(disable_days_week != '') {
            var exclude_dates_array = [];
            $.each( disable_days_week, function( key, value ) {
              if(value != '0') {
                var var_days_week = value - 1;
                exclude_dates_array.push(var_days_week);
              }
            });
            function disableSpecificWeekDays(date) {
              var theday = date.getDate() + '/' +
              (date.getMonth() + 1) + '/' +date.getFullYear();
              var day = date.getDay();
              if(jQuery.inArray(day, exclude_dates_array) !== -1) {
                return [false];
              } else {
                return [true];
              }
            }
            datesPickerData.beforeShowDay = disableSpecificWeekDays;
          }
        }

        $('.multiple-dates-field').each(function () {
          $(this).multiDatesPicker(datesPickerData);
        });
      }

      if($('.multiple-dates-calendar').length > 0) {
        $('.multiple-dates-calendar').each(function () {
          var dates = [];
          var numberOfMonths = [1,1];
          if($(this).attr('data-number-of-months').length > 0) {
            var numberOfMonths = $(this).attr('data-number-of-months').split(',');
          }
          if($(this).attr('data-dates').length > 0) {
            var datesArray = $(this).attr('data-dates').split(',');

            jQuery.each(datesArray, function(index, dateItem) {
              if (Date.parse(dateItem)) {
                const dateValue = new Date(dateItem);
                dates.push(dateValue);
              }
            });
          }

          $(this).multiDatesPicker({
            addDates: dates,
            numberOfMonths: numberOfMonths,
          });
        });
      }
    }
  };
})(jQuery, Drupal, drupalSettings);