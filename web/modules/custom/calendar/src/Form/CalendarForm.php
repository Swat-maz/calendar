<?php

namespace Drupal\calendar\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\Page;

/**
 *
 */
class CalendarForm extends FormBase {

  /**
   *
   */
  public function getFormId() {
    return 'calendar_form';
  }

  /**
   * Form constructor for the administrative listing/overview form.
   */
  public function buildForm($form, FormStateInterface $form_state) {
    $headers = ['Year','Jan','Feb','Mar','Q1','Apr','May','Jun','Q2','Jul','Aug','Sep','Q3','Oct','Nov','Dec','Q4','YTD'];

    $num_of_rows = $form_state->get('num_of_rows');
    if (empty($num_of_rows)){
      $num_of_rows=1;
      $form_state->set('num_of_rows', $num_of_rows);
    }

    $num_of_tables = $form_state->get('num_of_tables');
    if (empty($num_of_tables)){
      $num_of_tables=1;
      $form_state->set('num_of_tables', $num_of_tables);
    }



    $form['#tree'] = TRUE;

    $form['action']['add_row'] = [
      '#type' => 'submit',
      '#value' => 'Add Year',
      '#name' => 'add-row',
      '#submit' => ['::addYearButton'],
      '#ajax' => [
        'callback' => '::ajaxCallback',
        'event' => 'click',
        'wrapper' => 'my-form-wrapper',
      ],
    ];

    $form['action']['add_table'] = [
      '#type' => 'submit',
      '#value' => 'Add Table',
      '#name' => 'add-table',
      '#submit' => ['::addTableButton'],
      '#ajax' => [
        'callback' => '::ajaxCallback',
        'event' => 'click',
        'wrapper' => 'my-form-wrapper',
      ],
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => 'Submit',
      '#ajax' => [
        'callback' => '::ajaxCallback',
        'event' => 'click',
        'wrapper' => 'my-form-wrapper',
      ],
//      '#submit' => ['::validateForm'],
//      '#submit' => ['::addTableButton'],
//      '#ajax' => [
//        'callback' => '::addTableButtonAjax',
//        'event' => 'click',
//        'wrapper' => 'my-form-wrapper',
//      ],
    ];

    $form['wrapper'] = array(
//      '#tree' => TRUE,
      '#type'   => 'container',
      '#prefix' => '<div id="my-form-wrapper">',
      '#suffix' => '</div>',
    );

    for ($a = 0; $a < $num_of_tables; $a++) {
      $form['wrapper'][$a] = [
        '#type' => 'table',
        '#header' => $headers,
      ];

      for ($i = $num_of_rows; $i > 0; $i--) {
        $yearValues = date('Y', time() ) - $i+1;

        for ($c = 0; $c < count($headers); $c++){
          $var = $headers[$c];

          if ($var === "Year" || $var === "Q1" || $var === "Q2" || $var === "Q3" || $var === "Q4" || $var === "YTD") {
//            $disable = TRUE;
            if ($var === "Year") {
              $yearValue = date('Y', time() ) - $i+1;
              $form['wrapper'][$a][$yearValues][$var] = [
                '#type' => 'number',
                '#disabled' => TRUE,
                '#default_value' => $yearValue,
                '#title_display' => 'invisible',
              ];
            }elseif ($var === "Q1" || $var === "Q2" || $var === "Q3" || $var === "Q4" || $var === "YTD") {
              $form['wrapper'][$a][$yearValues][$var] = [
                '#type' => 'textfield',
                '#disabled' => TRUE,
                '#title_display' => 'invisible',
              ];
            }
          }else {
            $form['wrapper'][$a][$yearValues][$var] = [
              '#type' => 'number',
              '#title_display' => 'invisible',
            ];
          }

//          if ($var === "Year") {
//            $yearValue = date('Y', time() ) - $i;
//          }
//          else {
////            $yearValue = '123';
//            unset($yearValue);
//          }
//
//          $form['wrapper'][$a][$yearValues][$var] = [
//            '#type' => 'number',
////            '#disabled' => $disable,
//            $paste,
//            '#default_value' => $yearValue,
//            '#title_display' => 'invisible',
//          ];
//          unset($yearValues);
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

    $form['#attached']['library'] =  'calendar/calendar';
    return $form;
  }

  public function addYearButton(array &$form, FormStateInterface $form_state) {
    // Increase by 1 the number of rows.
    $num_of_rows = $form_state->get('num_of_rows');
    $num_of_rows++;
    $form_state->set('num_of_rows', $num_of_rows);

    // Rebuild form with 1 extra row.
    $form_state->setRebuild();
  }

  public function addTableButton(array &$form, FormStateInterface $form_state) {
    // Increase by 1 the number of rows.
    $num_of_tables = $form_state->get('num_of_tables');
    $num_of_tables++;
    $form_state->set('num_of_tables', $num_of_tables);

    // Rebuild form with 1 extra row.
    $form_state->setRebuild();
  }

  public function ajaxCallback(array &$form, FormStateInterface $form_state) {
    $form = $form_state->getCompleteForm();
    return $form['wrapper'];
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    //    parent::validateForm($form, $form_state);
    $trigeringElement = $form_state->getTriggeringElement();
    if ($trigeringElement['#value'] === "Submit") {

      $headers = [
        'Jan',
        'Feb',
        'Mar',
        'Apr',
        'May',
        'Jun',
        'Jul',
        'Aug',
        'Sep',
        'Oct',
        'Nov',
        'Dec'
      ];
      $numOfTables = $form_state->getStorage()['num_of_tables'];
      $numOfRow = $form_state->getStorage()['num_of_rows'];

      if ($numOfTables > 1) {
        // витягування усіх заповнених даних в масив
        for ($i = 0; $i < $numOfTables; $i++) {
          $allValues[$i] = $form_state->getUserInput()['wrapper'][$i];
        }
      }

      //    видаляє усі порожні значення з масивів
      //    for ($filterCounter = 0; $filterCounter < $numOfRow; $filterCounter ++) {
      //      $yearValues = date('Y', time() ) - $filterCounter;
      //      $filteredArray[$yearValues] = (array_filter($values[$yearValues]));
      //    }

      $simplCounter = 3;
      $masuvNeSpivpadin = [];
      for ($j = 1; $j <= $numOfTables - 1; $j++) {
        // форнікa для кількості рядків тобто для перевірки таблиць по роках
        for ($numberOfYears = 0; $numberOfYears < $numOfRow; $numberOfYears++) {
          $yearValues = date('Y', time()) - $numberOfYears;
          // форнікa для прирівняння чи однакові місяці в роках не порожні
          for ($i = 0; $i < count($headers); $i++) {
            $var = $headers[$i];
            if ($allValues[0][$yearValues][$var] !== "" && $allValues[$j][$yearValues][$var] !== "") {
              $simplCounter = 1;
            }
            elseif ($allValues[0][$yearValues][$var] == "" && $allValues[$j][$yearValues][$var] == "") {
              $simplCounter = 1;
            }
            else {
              $simplCounter = 0;
              if ($allValues[0][$yearValues][$var] === "") {
                $masuvNeSpivpadin[0][$yearValues][$var] = "$var";
              }
              elseif ($allValues[$j][$yearValues][$var] === "") {
                $masuvNeSpivpadin[$j][$yearValues][$var] = "$var";
              }
            }
          }
        }
      }
      // ерром месейдж при не однаковому заповненні таблиць
      if (!empty($masuvNeSpivpadin)) {
        return $form_state->setErrorByName('title', $this->t("Invalid"));
      }

      // валідація на нерозривність вводу
      if ($numOfRow >= 1) {
        $allValues = $form_state->getUserInput()['wrapper'][0];
        for ($key = 1; $key <= $numOfRow * 12; $key++) {
          $array_keys[] = $key;
        }
        foreach ($allValues as $values) {
          foreach ($values as $value) {
            $firstArray[] = $value;
          }
        }
        for ($firstCounter = 0; $firstCounter < count($array_keys); $firstCounter++) {
          $newAllValues[$array_keys[$firstCounter]] = $firstArray[$firstCounter];
        }
        $newAllValuesFiltered = array_filter($newAllValues);
        $arrayAllValuesKeys = array_keys($newAllValuesFiltered);
        for ($k = 0; $k < count($arrayAllValuesKeys) - 1; $k++) {
          if ($arrayAllValuesKeys[$k] + 1 != $arrayAllValuesKeys[$k + 1]) {
            return $form_state->setErrorByName('title', $this->t("Invalid"));
          }
        }
      }
      return $this->messenger()->addStatus($this->t('Valid'));
    }
  }

  /**
   *
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $numOfTables = $form_state->getStorage()['num_of_tables'];
    $numOfRow = $form_state->getStorage()['num_of_rows'];
    for ($i = 0; $i < $numOfTables; $i++) {
      $allValues[$i] = $form_state->getUserInput()['wrapper'][$i];
    }
    foreach ($allValues as $key => $tables){
      foreach ($tables as $years){
        $chunkedArray[$key][] = array_chunk($years, 3);
      }
    }
    foreach ($chunkedArray as $key => $tables){
      foreach ($tables as $yearKey => $years){
          foreach ($years as $quarterKey => $quarter){
            array_sum($quarter) == 0 ?
              $quarterSumArray[$key][$yearKey][$quarterKey] = array_sum($quarter) :
              $quarterSumArray[$key][$yearKey][$quarterKey] = round((array_sum($quarter)+1)/3, 2);
          }
      }
    }
    foreach ($quarterSumArray as $key => $tables){
      foreach ($tables as $yearKey => $years){
          array_sum($years) == 0 ?
            $yearSumArray[$key][$yearKey] = array_sum($years) :
            $yearSumArray[$key][$yearKey] = round((array_sum($years)+1)/4, 2);
      }
    }
    $quarters = ['Q1', 'Q2', 'Q3', 'Q4'];
    for ($j = $numOfRow; $j > 0; $j--){
      $yearArray[] = date('Y', time() ) - $j+1;
    }
    foreach ($quarterSumArray as $key => $tables){
      foreach ($tables as $yearKey => $years){
        foreach ($years as $quarterKey => $quarter)
          $quarter != 0 ? $form['wrapper'][$key][$yearArray[$yearKey]][$quarters[$quarterKey]]['#value'] = $quarter :
            $form['wrapper'][$key][$yearArray[$yearKey]][$quarters[$quarterKey]]['#value'] = "";
        }
      }
    foreach ($yearSumArray as $key => $tables){
      foreach ($tables as $yearKey => $years){
        $years != 0 ? $form['wrapper'][$key][$yearArray[$yearKey]]['YTD']['#value'] = $years :
            $form['wrapper'][$key][$yearArray[$yearKey]]['YTD']['#value'] = "";
      }
    }
//    $submitReturn['year'] = $yearSumArray;
//    $submitReturn['quarter'] = $quarterSumArray;

    return $form;
//    return $form['wrapper'][0][2021]['Q1']['#value'] = 10;
  }


}
