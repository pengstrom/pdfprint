<html>
  <?php
  require 'head.php';
  ?>
  <body>
    <div class="container">
      <div class="row">
        <div class="col-md-12">

          <div class="page-header">
            <h1><span class="fa fa-file-pdf-o"></span> <span class="pelle">Pelles</span> PDF-printer</h1>
            <p class="lead">Skriv ut enkelt på ITC</p>
          </div>

          <div class="jumbotron">
            <h1>Snabbutskrift</h1>
            <p>Om du bara vill skriva ut på pr2402 (skrivaren på våning 4 hus 2 ITC) kan du göra det här.</p>

            <br /> 
            <div class="row">
              <div class="col-sm-8 col-sm-offset-2">

                <div class="panel panel-primary">
                  <div class="panel-body">

                    <form action="quickprint.php" enctype="multipart/form-data" method="POST">
                      <div class="form-group">
                        <input class="form-control" type="text" name="username" id="username" value="" placeholder="Användarnamn"/>
                      </div>
                      <div class="form-group">
                        <input class="form-control" type="password" name="password" id="password" value="" placeholder="Lösenord A"/>
                      </div>
                      <div class="form-group">
                        <div class="checkbox">
                          <label>
                            <input type="checkbox" name="color" id="color" /> 
                            Färg
                          </label>
                        </div>
                        <div class="checkbox">
                          <label>
                            <input type="checkbox" name="duplex" id="duplex" checked="checked" /> 
                            Dubbelsidigt
                          </label>
                        </div>
                      </div>
                      <div class="form-group">
                        <input
                            class="form-control copies"
                            type="text"
                            id="copies"
                            name="copies"
                            placeholder="Kopior" />
                      </div>
                      <div class="form-group">
                        <input class="input-file" type="file" name="documents[]" id="document" multiple="multiple" /> 
                      </div>
                      <div class="form-group">
                        <div class="col-sm-8 no-horizontal-padding">

                          <p class="nudge-down"><a href="/printer.php">Avancerad utskrift</a></p>
                        </div>
                        <div class="col-sm-4 no-horizontal-padding">

                          <button class="btn btn-primary btn-lg pull-right" type="submit" value="Continue →"><span class="fa fa-print"></span> Skriv ut</button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>

              </div>
            </div>
                <p class="lead">Du kan också generara kommandon för skrivarna <a href="/commandprinter.php" target="">här.</a></p>
          </div>

          <div class="col-sm-8 col-sm-offset-2">

          </div>
        </div> 
      </div> 
    </div>
    <?php
      require 'foot.php';
    ?>
  </body>
</html>
