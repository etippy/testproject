<!DOCTYPE html>
<html lang="en">
<head>
  <title>Commission Fee Calculator</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<div class="container-fluid p-5 bg-primary text-white text-center">
  <h1>Commission Fee Calculator</h1>
</div>
  
<div class="container mt-5">

    <div class="row">
        <div class="col-sm-6 p-3">
            <div class="alert alert-info">
                <strong>Info!</strong> Currancy exchange values come from the API so calculation of the fee might be different from testing env.
            </div>
            <form action="/calculate-commissions" method="post" enctype="multipart/form-data">
                @csrf

                <div class="input-group">
                    <input type="file" id="csv_file" name="csv_file" accept=".csv" required class="form-control">
                    <span class="input-group-text">SV File:</span>
                </div>
                <hr>
                <button type="submit" class="btn btn-success">Calculate Commissions</button>
                <hr>
                @if(isset($results))
                    <h3 class="title">Result:</h3>
                    @foreach($results as $value)
                       {{ $value }}<br>
                    @endforeach
                @endif
            </form>
        </div>
        <div class="col-sm-6 p-3 bg-dark text-white">
            <div class="alert alert-danger">
                <strong>Info!</strong> This is test env. currancy exchanges are static so it will not be changed. <b>exchange rates: EUR:USD - 1:1.1497, EUR:JPY - 1:129.53.</b>
            </div>
            <form action="/calculate-commissions-test" method="post" enctype="multipart/form-data">
                @csrf

                <div class="input-group">
                    <input type="file" id="csv_file_test" name="csv_file_test" accept=".csv" required class="form-control">
                    <span class="input-group-text">SV File:</span>
                </div>
                <hr>
                <button type="submit" class="btn btn-success">Calculate Commissions</button>
                <hr>
                @if(isset($testResults))
                    <h3 class="title">Result:</h3>
                    @foreach($testResults as $value)
                       {{ $value }}<br>
                    @endforeach
                @endif
            </form>
        
        </div>
    </div>


    

        

        
        
     
</div>

</body>
</html>

