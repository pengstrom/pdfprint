<html>
  <?php

  require 'head.php';


  $jsonfile = '../printer-options.json';

  $printerOptions = (array) json_decode(file_get_contents($jsonfile));
  $printers = array_keys($printerOptions);
  sort($printers);

  ?>


  <body>
    <div class="container">
      <div class="page-header">
        <h1> <span class="fa fa-print"></span> Välj en skrivare</h1>
        <p class="lead">
        Efter att du valt en skrivare kommer du få se vilka inställningar man kan göra på just den skrivaren.
        </p>
      </div>
      <div class="row">
        <div class="col-sm-10 col-sm-offset-1">

          <form class="form-horizontal" action="commandoptions.php" method="post">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <h3 class="panel-title">Skrivare</h3>
              </div>
              <div class="panel-body">
                
              <div class="row">
                <div class="col-sm-4 col-sm-offset-4">
                  <select class="form-control input-lg" name="printer" id="printer">
                    <?php
                    foreach ($printers as $printer) {
                        $printer = htmlspecialchars($printer);
                        if ($printer == 'pr2402') {
                          echo '<option default>' . $printer . '</option>';
                        } else {
                          echo '<option>' . $printer . '</option>';
                        }
                    }
                    ?> 
                  </select>
                </div>
              </div>
              </div>
            </div>

              <button type="submit" class="btn btn-lg btn-primary pull-right">
                Välj fil och inställningar <span class="fa fa-arrow-circle-right fa-lg"></span>
              </button>
          </form>

        </div>
      </div>
    </div>
  </body>
</html>
