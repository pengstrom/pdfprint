<html>
  <?php

  require 'head.php';

  require '../vendor/autoload.php';

  $printer = $_POST['printer'];

  $jsonfile = '../printer-options.json';

  $options = (array) json_decode(file_get_contents($jsonfile));
  $printerOptions = (array) $options[$printer];
  ksort($printerOptions);

  ?>


  <body>
    <div class="container">
      <div class="page-header">
        <h1> <span class="fa fa-cog"></span> Välj fil och inställningar</h1>
        <p class="lead">
        De förvalda inställningarna är standardalternativen för den här skrivaren. Ändra bara inställningar om du vet vad de gör!
        </p>
      </div>
      <div class="row">
        <div class="col-sm-10 col-sm-offset-1">

          <form class="form-horizontal" enctype="multipart/form-data" action="print.php" method="post">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <h3 class="panel-title">Dokument och autentsiering</h3>
              </div>
              <div class="panel-body">
                <div class="col-sm-10 col-sm-offset-1">
                  <div class="col-sm-10 col-sm-offset-1">
                    
                  
                <div class="form-group">
                  <input placeholder="Användarnamn" class="form-control" type="text" name="username" id="username" value="" />
                </div>
                <div class="form-group">
                  <input placeholder="Lösenord" class="form-control" type="password" name="password" id="password" value="" />
                </div>
                  </div>
                <div class="form-group">
                  <input type="file" name="document" id="document"/>
                </div>
                </div>

              </div>
            </div>

            <input type="hidden" name="printer" id="printer" value="<?=$printer?>" />

            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">Inställningar för <strong><?=$printer?></strong></h3>
              </div>
              <div class="panel-body">
                <?php
                   foreach ($printerOptions as $optionName => $optionData) {
                   $default = $optionData->default;
                   $options = (array) $optionData->values;
                   $optionDescription = $optionData->description;

                   echo '<div class="form-group">';
                     echo '<label class="control-label col-sm-6" for="printer">' . $optionDescription . '</label>';
                     echo '<div class="col-sm-5">';
                       echo '<select class="form-control" name="' . $optionName . '" id="' . $optionName. '">';
                         foreach ($options as $option) {
                         d($option);
                         if ($option === $default) {
                         echo '<option selected="selected">' . $option . '</option>';
                         } else {
                         echo '<option>' . $option . '</option>';
                         }
                         }
                         echo '</select>';
                       echo '</div>';
                     echo '</div>';
                   }
                   ?>

              </div> 
            </div>
            <button type="submit" class="btn btn-primary btn-lg pull-right">
              <span class="fa fa-print"></span> Skriv ut
            </button>
          </form>
        </div>
      </div>
    </div>
  </body>
</html>
