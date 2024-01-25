<?php


namespace Drupal\multiple_dates\Plugin\Field\FieldFormatter;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Field\Annotation\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'multiple_dates' formatter.
 *
 * @FieldFormatter(
 *   id = "multiple_dates_default_formatter",
 *   module = "multiple_dates",
 *   label = @Translation("Multiple Dates "),
 *   field_types = {
 *     "multiple_dates"
 *   }
 * )
 */
class MultipleDatesDefaultFormatter extends FormatterBase {

 /**
   * {@inheritdoc}
   */
 public static function defaultSettings() {
    return [
      'number_of_months' => '1,1',
      'display_layout' => 'calendar_view',
      'date_format' => 'F j, Y',
  ] + parent::defaultSettings();
}

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::settingsForm($form, $form_state);

    $element['display_layout'] = [
        '#type' => 'select',
        '#title' => $this->t('Months display layout'),
        '#description' => $this->t('Select the layout of the months to be displayed'),
        '#options' => [
            'comma_separated' => $this->t('Comma separated'),
            'list_view' => $this->t('List view'),
            'calendar_view' => $this->t('Calendar view')
        ],
        '#default_value' => $this->getSetting('display_layout')??'calendar_view',
        '#required' => false
    ];
    $element['date_format'] = [
        '#type' => 'select',
        '#title' => $this->t('Date format'),
        '#description' => $this->t('Select the format of the date to be displayed'),
        '#options' => [
            'F j, Y' => $this->t(date('F j, Y')),
            'jS F, Y' => $this->t(date('jS F, Y')),
            'm.d.y' => $this->t(date('m.d.y')),
            'Ymd' => $this->t(date('Ymd')),
            'D M j, Y' => $this->t(date('D M j, Y')),
            'Y-m-d' => $this->t(date('Y-m-d')),
            'd-m-Y' => $this->t(date('d-m-Y')),
            'j-M-Y' => $this->t(date('j-M-Y')),
            'F j' => $this->t(date('F j')),
            'jS F' => $this->t(date('jS F')),
        ],
        '#default_value' => $this->getSetting('date_format')??'F j, Y',
        '#required' => false,
        '#states' => [
            'visible' => [
                ':input[name$="[settings_edit_form][settings][display_layout]"]' => ['!value' => 'calendar_view'],
            ],
        ]
    ];
    $element['number_of_months'] = [
        '#type' => 'select',
        '#title' => $this->t('Number of months'),
        '#description' => $this->t('Select the number of months to display on the calendar'),
        '#options' => [
            '1,1' => $this->t('1x1'),
            '1,2' => $this->t('1x2'),
            '1,3' => $this->t('1x3'),
            '2,2' => $this->t('2x2'),
            '2,3' => $this->t('2x3'),
            '3,3' => $this->t('3x3'),
            '3,4' => $this->t('3x4')
        ],
        '#default_value' => $this->getSetting('number_of_months')??'1,1',
        '#required' => false,
        '#states' => [
            'visible' => [
                ':input[name$="[settings_edit_form][settings][display_layout]"]' => ['value' => 'calendar_view'],
            ],
        ]
    ];

    return $element;
}

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $display_layout = $this->getSetting('display_layout');
    $date_format = $this->getSetting('date_format');
    $number_of_months = $this->getSetting('number_of_months');

    $summary_content = 'Display layout: '.$display_layout.'<br>';

    $number_of_months = str_replace(',', 'x', $number_of_months);

    if($display_layout == 'calendar_view') {
        $summary_content .= 'This number of months to display: '.$number_of_months;
    } else {
        $summary_content .= 'The the date format: '.$date_format;
    }

   // Implement settings summary.
    $summary[] = $this->t($summary_content);

    return $summary;
}

    /**
     * Builds a renderable array for a field value.
     *
     * @param \Drupal\Core\Field\FieldItemListInterface $items
     *   The field values to be rendered.
     * @param string $langcode
     *   The language that should be used to render the field.
     *
     * @return array
     *   A renderable array for $items, as an array of child elements keyed by
     *   consecutive numeric indexes starting from 0.
     */
    public function viewElements(FieldItemListInterface $items, $langcode)
    {
        $elements = array();
        $number_of_months = $this->getSetting('number_of_months');
        $display_layout = $this->getSetting('display_layout');
        $other_data['number_of_months'] = $number_of_months;
        $other_data['display_layout'] = $display_layout;

        if($display_layout != 'calendar_view') {
            $date_format = $this->getSetting('date_format');
            $other_data['date_format'] = $date_format;
        }

        foreach ($items as $delta => $item) {
            $formatted_dates = array();
            if($display_layout != 'calendar_view') {
                $all_dates = $item->multiple_dates;
                if($all_dates != '') {
                    $formatted_dates_values = array_map('trim', explode(',', $all_dates));
                    foreach ($formatted_dates_values as $key => $date_value) {
                        $formatted_dates[] = get_formatted_date($date_value, $date_format);
                    }

                    if($display_layout == 'comma_separated') {
                        $formatted_dates = "<span class='comma-separated-date'>".implode('</span>, <span class="comma_separated-date">', $formatted_dates)."</span>";

                    }
                }
                $other_data['formatted_dates'] = $formatted_dates;
            }

            $elements[$delta] = array(
                '#theme' => 'multiple_dates',
                '#multiple_dates' => $item->multiple_dates,
                '#other_data' => $other_data,
            );
        }

        return $elements;
    }
}