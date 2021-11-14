<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Person List</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    </head>
    <body>
        <div class="contianer-fluid">
            <div class="jumbotron-fluid p-5">
                <div class="row">
                    <h1>Top Secret CIA Database</h1>
                </div>
                <div>
                    <form class="row g-3 submit-form" method="get" action="">
                        <div class="col-auto">
                            <label for="year">Birth year</label>
                            <input type="number" class="form-control" id="year" placeholder="1991" value="{{$year}}">
                        </div>
                        <div class="col-auto">
                            <label for="month">Month</label>
                            <input type="number" class="form-control" id="month" placeholder="12" value="{{$month}}">
                        </div>
                        <div class="col-auto">
                            <label for="limit">Count</label>
                            <input type="number" class="form-control" id="limit" value="{{$limit}}">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary mt-4 mb-3">Filter</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-12">
                    <div class="row my-3">
                        <div class="col-sm-6"><b><?= !empty($total) ? $total . " people in the list" : ""?></b></div>
                        <?php if (!empty($prev_page_url) || !empty($next_page_url)) { ?>
                            <div class="col-sm-6">
                                <b>
                                    <span id="previous" data-url="<?= !empty($prev_page_url) ? $prev_page_url : '' ?>">Previous </span>
                                </b>
                                <span class="px-5"> Showing result {{$from ?? ''}} - {{$to ?? ''}}</span>
                                <b>
                                    <span id="next" data-url="<?= !empty($next_page_url) ? $next_page_url : '' ?>"> Next</span>
                                </b>
                            </div>
                        <?php } ?>
                    </div>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Email</th>
                                <th>Name</th>
                                <th>Birthday</th>
                                <th>Phone</th>
                                <th>IP</th>
                                <th>Country</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($result)) { ?>
                                <tr>
                                    <th>Sorry, No record found!</th>
                                    <th> </th>
                                    <th> </th>
                                    <th> </th>
                                    <th> </th>
                                    <th> </th>
                                    <th> </th>
                                </tr>
                            <?php } ?>
                            <?php foreach ($result as $record) { ?>
                                <tr>
                                    <td scope="row">{{$record->id}}</td>
                                    <td>{{$record->email}}</td>
                                    <td>{{$record->name}}</td>
                                    <td>{{$record->birthday}}</td>
                                    <td>{{$record->phone}}</td>
                                    <td>{{$record->ip}}</td>
                                    <td>{{$record->country}}</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!--local styling-->
        <style>
            #next, #previous{
                cursor: pointer;
            }
        </style>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script>
            
            function getParams(){
                let month = "";
                if ($("#month").val()) {
                    month = "&month=" + $("#month").val();
                }

                let year = "";
                if ($("#year").val()) {
                    year = "&year=" + $("#year").val();
                }

                let limit = "";
                if ($("#limit").val()) {
                    limit = "&limit=" + $("#limit").val();
                }

                let parameters = month + year + limit;
                
                return parameters;
            }
            
            $(".submit-form").submit(function (e) {
                e.preventDefault;
                let parameters = getParams();
                window.location.href = '/persons?' + parameters;
                return false;
            });
            
            $("#next").click(function (){
                let url = $("#next").attr("data-url");
                let parameters = getParams();
                if (url){
                    window.location.href = url + parameters;
                }
            });
            
            $("#previous").click(function (){
                let url = $("#previous").attr("data-url");
                let parameters = getParams();
                if (url){
                    window.location.href = url + parameters;
                }
            });
        </script>
    </body>
</html>
