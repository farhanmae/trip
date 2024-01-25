<?php
namespace Drupal\multiple_dates\Plugin\Field\FieldWidget;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Field\Annotation\FieldWidget;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Field\WidgetInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'multiple_dates_default_widget' widget.
 *
 * @FieldWidget(
 *   id = "multiple_dates_default_widget",
 *   label = @Translation("MultipleDates Widget"),
 *   field_types = {
 *     "multiple_dates"
 *   }
 * )
 */

class MultipleDatesDefaultWidget extends WidgetBase implements WidgetInterface  {
    /**
     * Returns the form for a single field widget.
     *
     * Field widget form elements should be based on the passed-in $element, which
     * contains the base form element properties derived from the field
     * configuration.
     *
     * The BaseWidget methods will set the weight, field name and delta values for
     * each form element. If there are multiple values for this field, the
     * formElement() method will be called as many times as needed.
     *
     * Other modules may alter the form element provided by this function using
     * hook_field_widget_form_alter() or
     * hook_field_widget_WIDGET_TYPE_form_alter().
     *
     * The FAPI element callbacks (such as #process, #element_validate,
     * #value_callback, etc.) used by the widget do not have access to the
     * original $field_definition passed to the widget's constructor. Therefore,
     * if any information is needed from that definition by those callbacks, the
     * widget implementing this method, or a
     * hook_field_widget[_WIDGET_TYPE]_form_alter() implementation, must extract
     * the needed properties from the field definition and set them as ad-hoc
     * $element['#custom'] properties, for later use by its element callbacks.
     *
     * @param \Drupal\Core\Field\FieldItemListInterface $items
     *   Array of default values for this field.
     * @param int $delta
     *   The order of this item in the array of sub-elements (0, 1, 2, etc.).
     * @param array $element
     *   A form element array containing basic properties for the widget:
     *   - #field_parents: The 'parents' space for the field in the form. Most
     *       widgets can simply overlook this property. This identifies the
     *       location where the field values are placed within
     *       $form_state->getValues(), and is used to access processing
     *       information for the field through the getWidgetState() and
     *       setWidgetState() methods.
     *   - #title: The sanitized element label for the field, ready for output.
     *   - #description: The sanitized element description for the field, ready
     *     for output.
     *   - #required: A Boolean indicating whether the element value is required;
     *     for required multiple value fields, only the first widget's values are
     *     required.
     *   - #delta: The order of this item in the array of sub-elements; see $delta
     *     above.
     * @param array $form
     *   The form structure where widgets are being attached to. This might be a
     *   full form structure, or a sub-element of a larger form.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *   The current state of the form.
     *
     * @return array
     *   The form elements for a single widget for this field.
     *
     * @see hook_field_widget_form_alter()
     * @see hook_field_widget_WIDGET_TYPE_form_alter()
     */
    public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state)
    {

        foreach ($element as $key => $value) {
            $element['multiple_dates'][$key] = $value;
        }

        $settings_data['date_format'] = $this->getSetting('date_format');
        $settings_data['date_selection_type'] = $this->getSetting('date_selection_type');
        $settings_data['maximum_picks_data'] = $this->getSetting('maximum_picks_data');
        $settings_data['days_range_data'] = $this->getSetting('days_range_data');
        $settings_data['exclude_date'] = $this->getSetting('exclude_date');
        $settings_data['min_date'] = $this->getSetting('min_date');
        $settings_data['max_date'] = $this->getSetting('max_date');
        $settings_data['disable_days_week'] = $this->getSetting('disable_days_week');
        $settings_data['change_year'] = ($this->getSetting('change_year') && $this->getSetting('change_year') == 1)?true:false;
        $settings_data['change_month'] = ($this->getSetting('change_month') && $this->getSetting('change_month') == 1)?true:false;
        $settings_data['number_of_months'] = $this->getSetting('number_of_months')??'';

        $element['multiple_dates']['#type'] = 'textfield';
        $element['multiple_dates']['#default_value'] = (isset($items[$delta]->multiple_dates))?$items[$delta]->multiple_dates: NULL;

        $element['multiple_dates']['#attached']['drupalSettings']['settings_data'] =  $settings_data;
        $element['multiple_dates']['#attached']['library'][] = 'multiple_dates/multiple_dates';
        $element['multiple_dates']['#attributes']['class'][] = 'multiple-dates-field';

        $element['multiple_dates']['#element_validate'] = [
            [$this, 'validate'],
        ];

        return $element;
    }


  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {

      return [
       'date_format' => 'yy-mm-dd',
       'date_selection_type' => '',
       'maximum_picks_data' => '',
       'days_range_data' => '',
       'exclude_date' => '',
       'min_date' => '',
       'max_date' => '',
       'disable_days_week' => '',
       'change_year' => '',
       'change_month' => '',
       'number_of_months' => '',
   ] + parent::defaultSettings();
}

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = [];
    $elements['date_format'] = [
        '#type' => 'select',
        '#title' => $this->t('Custom date format'),
        '#description' => $this->t('Custom date format'),
        '#options' => [
            'yy-mm-dd' => $this->t('yy-mm-dd'),
            'dd-mm-yy' => $this->t('dd-mm-yy'),
        ],
        '#default_value' => $this->getSetting('date_format')??'yy-mm-dd',
        '#required' => TRUE,
        '#access' => false
    ];
    $elements['date_selection_type'] = [
        '#type' => 'select',
        '#title' => $this->t('Date select type'),
        '#description' => $this->t('Date select type'),
        '#options' => [
            '' => $this->t('All'),
            'maximum_picks' => $this->t('Set maximum picks'),
            'days_range' => $this->t('Days range'),
        ],
        '#default_value' => $this->getSetting('date_selection_type')??'',
        '#required' => false
    ];

    //maximum_picks
    $elements['maximum_picks_data'] = [
        '#type' => 'number',
        '#title' => $this->t('Set maximum picks'),
        '#description' => $this->t('Set the maximum number of dates that can be picked.'),
        '#default_value' => $this->getSetting('maximum_picks_data'),
        '#required' => FALSE,
        '#states' => [
            'visible' => [
                ':input[name$="[settings_edit_form][settings][date_selection_type]"]' => ['value' => 'maximum_picks'],
            ],
        ]
    ];


    //days_range
    $elements['days_range_data'] = [
        '#type' => 'number',
        '#title' => $this->t('Days range'),
        '#description' => $this->t('This way you can automatically select a range of days with respect to the day clicked.
            <br>

            In this example the day range is set to 5, which means from the day clicked to 5 days in advance.'),
        '#default_value' => $this->getSetting('days_range_data'),
        '#required' => FALSE,
        '#states' => [
            'visible' => [
                ':input[name$="[settings_edit_form][settings][date_selection_type]"]' => ['value' => 'days_range'],
            ],
        ],
    ];

    $sample_date = date('Y-m-d');

    $elements['exclude_date'] = [
        '#type' => 'textarea',
        '#title' => $this->t('Disable specific dates from calendar'),
        '#description' => $this->t('you can specify some dates to disable.
            Dates in the array can be a mix of object date and string dates.
            <br>
            Enter days in following format YYYY-MM-DD e.g. '.$sample_date.'. Separate multiple dates with comma. This is used for specific dates, if you want to disable all weekends use settings above, not this field.'),
        '#default_value' => $this->getSetting('exclude_date'),
        '#required' => FALSE,
        '#states' => [
            'visible' => [
                ':input[name$="[settings_edit_form][settings][date_selection_type]"]' => ['!value' => 'days_range'],
            ],
        ],
    ];

    $elements['min_date'] = [
        '#type' => 'number',
        '#title' => $this->t('Min date'),
        '#description' => $this->t('As with the jQuery Datespicker, you can define a minimum date from where to pick dates.
            The values are relative to the current date. For eg. 0 for today, -1 for yesterday, 1 for tomorrow etc.
            '),
        '#default_value' => $this->getSetting('min_date'),
        '#required' => FALSE,
        '#states' => [
            'visible' => [
                ':input[name$="[settings_edit_form][settings][date_selection_type]"]' => ['!value' => 'days_range'],
            ],
        ],
    ];
    $elements['max_date'] = [
        '#type' => 'number',
        '#title' => $this->t('Max date'),
        '#description' => $this->t('As with the jQuery Datespicker, you can define a maximum date from where to pick dates.
            The values are relative to the current date. For eg. if we set 30, it will be +30 days from today'),
        '#default_value' => $this->getSetting('max_date'),
        '#required' => FALSE,
        '#states' => [
            'visible' => [
                ':input[name$="[settings_edit_form][settings][date_selection_type]"]' => ['!value' => 'days_range'],
            ],
        ],
    ];

    $elements['disable_days_week'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Disable specific days in week'),
      '#description' => $this->t('Select days which are disabled in calendar, etc. weekends or just Friday'),
      '#options' => [
        '1' => $this->t('Sunday'),
        '2' => $this->t('Monday'),
        '3' => $this->t('Tuesday'),
        '4' => $this->t('Wednesday'),
        '5' => $this->t('Thursday'),
        '6' => $this->t('Friday'),
        '7' => $this->t('Saturday'),
    ],
    '#default_value' => $this->getSetting('disable_days_week'),
    '#required' => FALSE,
    '#states' => [
        'visible' => [
            ':input[name$="[settings_edit_form][settings][date_selection_type]"]' => ['!value' => 'days_range'],
        ],
    ],
];

$elements['change_year'] = [
    '#type' => 'checkbox',
    '#title' => $this->t('Enable change year'),
    '#description' => $this->t('Enable to change year'),
    '#default_value' => $this->getSetting('change_year')??'false',
];

$elements['change_month'] = [
    '#type' => 'checkbox',
    '#title' => $this->t('Enable change month'),
    '#description' => $this->t('Enable to change month'),
    '#default_value' => $this->getSetting('change_month')??'false',
];

$elements['number_of_months'] = [
    '#type' => 'select',
    '#title' => $this->t('Number Of Months'),
    '#description' => $this->t('Display number of months'),
    '#options' => [
        '1,1' => $this->t('1x1'),
        '1,2' => $this->t('1x2'),
        '1,3' => $this->t('1x3'),
        '2,2' => $this->t('2x2'),
        '2,3' => $this->t('2x3'),
        '3,3' => $this->t('3x3'),
        '3,4' => $this->t('3x4')
    ],
    '#default_value' => $this->getSetting('number_of_months')??'',
    '#required' => false
];


return $elements;
}

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = array();

    $date_format = $this->getSetting('date_format');
    $date_selection_type = $this->getSetting('date_selection_type');
    $days_range_data = $this->getSetting('days_range_data');
    $exclude_date = $this->getSetting('exclude_date');
    $min_date = $this->getSetting('min_date');
    $max_date = $this->getSetting('max_date');
    $disable_days_week = $this->getSetting('disable_days_week');
    $change_year = ($this->getSetting('change_year') && $this->getSetting('change_year') == 1)?true:false;
    $change_month = ($this->getSetting('change_month') && $this->getSetting('change_month') == 1)?true:false;

    $disabled_days = array();
    if(is_array($disable_days_week)) {
        $days = [
          1 => 'Sunday',
          2 => 'Monday',
          3 => 'Tuesday',
          4 => 'Wednesday',
          5 => 'Thursday',
          6 => 'Friday',
          7 => 'Saturday'
      ];

      foreach ($disable_days_week as $key => $day_num) {
        if($day_num != 0) {
            $disabled_days[] = $days[$day_num];
        }
    }
}

$disabled_days_list = implode(', ', $disabled_days);

$summary_content = 'Date format: '.$date_format.'<br>';
$summary_content .= 'Date selection type: '.$date_selection_type.'<br>';
$summary_content .= 'Days range data: '.$days_range_data.'<br>';
$summary_content .= 'Excluded date(s): '.$exclude_date.'<br>';
$summary_content .= 'Min date: '.$min_date.'<br>';
$summary_content .= 'Max date: '.$max_date.'<br>';
$summary_content .= 'Disable days week: '.$disabled_days_list.'<br>';
$summary_content .= 'Enable change year: '.$change_year.'<br>';
$summary_content .= 'Enable change month: '.$change_month.'<br>';

   // Implement settings summary.
$summary[] = $this->t($summary_content);

    // $summary[] = t('Multi select: @value', array('@value' => ($this->getSetting('multi_select') ? 'true' : 'false')));

return $summary;
}

  /**
   * Validate multiple dates field
   */
  public function validate($element, FormStateInterface $form_state) {
    $dates_value = $element['#value'];

    $date_format = $this->getSetting('date_format');
    $date_selection_type = $this->getSetting('date_selection_type');
    $days_range_data = $this->getSetting('days_range_data');
    $exclude_date = $this->getSetting('exclude_date');
    $min_date = $this->getSetting('min_date');
    $max_date = $this->getSetting('max_date');
    $disable_days_week = $this->getSetting('disable_days_week');
    $change_year = ($this->getSetting('change_year') && $this->getSetting('change_year') == 1)?true:false;
    $change_month = ($this->getSetting('change_month') && $this->getSetting('change_month') == 1)?true:false;

    $error_message = '';
    $dates_value_arr = array_map('trim', explode(',', $dates_value));
    $start_date = $dates_value_arr[0];
    $end_date = end($dates_value_arr);

    if($date_format == 'yy-mm-dd') {
        $date_format = 'Y-m-d';

    } else if($date_format == 'dd-mm-YY') {
        $date_format = 'd-m-Y';
    }

    switch ($date_selection_type) {
     case 'maximum_picks':
     $maximum_picks_data = $this->getSetting('maximum_picks_data');

     $dates_value_arr_count = count($dates_value_arr);
     if($dates_value_arr_count > $maximum_picks_data) {
        $error_message = 'Invalid dates';
    }

    break;

    case 'days_range':
    $end_date_range = date($date_format, strtotime($start_date. ' + '.$days_range_data.' days'));
    if($end_date != $end_date_range) {
        $error_message = 'Please select the dates between valid range.';
    }

    break;
}

if($exclude_date != '') {
    $exclude_date_arr = array_map('trim', explode(',', $exclude_date));
    $common_dates = array_intersect($exclude_date_arr, $dates_value_arr);
    if (count($common_dates) > 0) {
       $error_message = 'Disabled values can not selected.';
   }
}

if($min_date != '') {
    $min_date_value = date($date_format, strtotime(' + '.$min_date.' days'));

    $min_date_value_timestamp = strtotime($min_date_value);
    $start_date_timestamp = strtotime($start_date);

    if($start_date_timestamp < $min_date_value_timestamp) {
        $error_message = 'Start date does not match.';
    }
}

if($max_date != '') {
    $max_date_value = date($date_format, strtotime(' + '.$max_date.' days'));
    $max_date_value_timestamp = strtotime($max_date_value);
    $end_date_timestamp = strtotime($end_date);

    if($end_date_timestamp > $max_date_value_timestamp) {
        $error_message = 'End date does not match.';
    }
}

if(is_array($disable_days_week) && !empty($disable_days_week)) {
    $disabled_weeks = array();
    foreach ($disable_days_week as $key => $week) {
        if($week != '0') {
            $disabled_weeks[] = $week;
        }
    }

    $week_days = array();

    foreach ($dates_value_arr as $key => $date_value) {
        $date = strtotime($date_value);
        $week_day = date('N', $date);
        $week_days[] = $week_day;
    }

    $common_dates = array_intersect($week_days, $disabled_weeks);
    if (count($common_dates) > 0) {
        $error_message = 'Disabled week dates can not selected.';
    }
}

if($error_message != '') {
    $form_state->setError($element, t($error_message));
}

}
}