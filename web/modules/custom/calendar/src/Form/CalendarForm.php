<?php

namespace Drupal\calendar\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 *
 */
class CalendarForm extends FormBase {

  /**
   * Form constructor for the administrative listing/overview form.
   */
  public function buildForm($form, FormStateInterface $form_state) {
    $headers = ['Year','Jan','Feb','Mar','Q1','Apr','May','Jun','Q2','Jul','Aug','Sep','Q3','Oct','Nov','Dec','Q4','YTD'];
    $num_of_rows = $form_state->get('num_of_rows');
    if (empty($num_of_rows)){
      $num_of_rows=0;
      $form_state->set('num_of_rows', $num_of_rows);
    }
    $num_of_tables = $form_state->get('num_of_tables');
    if (empty($num_of_tables)){
      $num_of_tables=0;
      $form_state->set('num_of_tables', $num_of_tables);
    }
    $form['action']['add_table'] = [
      '#type' => 'submit',
      '#value' => 'Add Table',
      '#submit' => ['::addTableButton'],
      //      '#ajax' => [
      //        'callback' => '::addYearButton',
      //        'event' => 'click',
      //      ],
    ];
    for ($a = 1; $a <= $num_of_tables; $a++) {
      $form['action']['add_row'] = [
        '#type' => 'submit',
        '#value' => 'Add year',
        '#submit' => ['::addYearButton'],
        //      '#ajax' => [
        //        'callback' => '::addYearButton',
        //        'event' => 'click',
        //      ],
      ];
      $form[$a] = [
        '#type' => 'table',
        '#header' => $headers,
      ];
      for ($i = 0; $i <= $num_of_rows; $i++) {
        $form[$a][$i]['#attributes'] = [
          'class' => [
            'foo',
            'baz',
          ],
        ];
//        $form[$a][$i]['year'] = [
//          '#type' => 'number',
//          '#title_display' => 'invisible',
//          '#value' => date('Y', time() ) - $i,
//          '#disabled' => TRUE,
//        ];
        for ($c = 0; $c <= count($headers)-1; $c++){
          $var = $headers[$c];
          if ($var === "Year" || $var === "Q1" || $var === "Q2" || $var === "Q3" || $var === "Q4" || $var === "YTD") {
            $disable = TRUE;
            }
          else {
            $disable = FALSE;
          }
          if ($var === "Year") {
            $yearValue = date('Y', time() ) - $i;
          }
          else {
            $yearValue = '';
          }
          $form[$a][$i][$var] = [
            '#type' => 'number',
            '#disabled' => $disable,
            '#value' => $yearValue,
            '#title_display' => 'invisible',
          ];
        }
      }
    }
//    $form['contacts'][]['colspan_example'] = [
//      '#plain_text' => 'Colspan Example',
//      '#wrapper_attributes' => [
//        'colspan' => 100,
//        'class' => [
//          'foo',
//          'bar',
//        ],
//      ],
//    ];

    $form['#attached']['library'] = 'calendar/calendar';
    return $form;
  }

  /**
   *
   */
  public function getFormId() {
    return 'calendar_form';
  }

  /**
   *
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // @todo Implement submitForm() method.
  }

  public function addYearButton(array &$form, FormStateInterface $form_state) {
//    $ajax_response = new AjaxResponse();
    // Increase by 1 the number of rows.
    $num_of_rows = $form_state->get('num_of_rows');
    $num_of_rows++;
    $form_state->set('num_of_rows', $num_of_rows);

    // Rebuild form with 1 extra row.
    $form_state->setRebuild();
    return $form_state;
  }

  public function addTableButton(array &$form, FormStateInterface $form_state) {
    //    $ajax_response = new AjaxResponse();
    // Increase by 1 the number of rows.
    $num_of_tables = $form_state->get('num_of_tables');
    $num_of_tables++;
    $form_state->set('num_of_tables', $num_of_tables);

    // Rebuild form with 1 extra row.
    $form_state->setRebuild();
    return $form_state;
  }

}
