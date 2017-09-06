<div class="contentWrapper">
    <?php
    //Get variables
    $vacancies_opened = $vars['opened'];
    $vacancies_closed = $vars['closed'];
    
    $select = elgg_echo('vacancy:select_type');
    $select_input = elgg_view('input/select', array('name' => 'select_type', 'id' => 'select_type', 'options_values' => array('open' => elgg_echo('vacancy:opened_vacancies'), 'close' => elgg_echo('vacancy:closed_vacancies'))));
    ?>

    
    <script>
    //Show or hide entity list according if we need to show opened or closed vacancies
    $(document).ready(function () {
       $('#select_type').change(function () {
          var value = $('#select_type option:selected').val();
          if (value === "open") {
             $('#vacancy_opened').show();
             $('#vacancy_closed').hide();
          } else {
             $('#vacancy_closed').show();
             $('#vacancy_opened').hide();
          }
       });
    });</script>

    <br>
    <p>
    <b>
    <?php echo $select; ?></b><br>
    <?php echo $select_input; ?>
    </p><br>
    <p>
    <div id="vacancy_opened"><?php 
    foreach ($vacancies_opened as $one_vacancy){
       echo elgg_view("object/vacancy",array('full_view' => false,'entity'=>$one_vacancy));
    }
    ?></div>
    <div id="vacancy_closed" style="display: none;"><?php 
    foreach ($vacancies_closed as $one_vacancy){
       echo elgg_view("object/vacancy",array('full_view' => false,'entity'=>$one_vacancy));
    }
    ?></div>
    </p>

</div>