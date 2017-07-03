<!doctype html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

        <title>Converter</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <script>
            $(function(){
                $('input[type=file]').on('change',function(){
                    var extention = $(this).val().toLowerCase();
                    if(valid(this, extention)){
                        $('input[type=radio]').each(function(){
                            var ext = extention.split('.');
                            var p = '';
                            p = ext[1];
                            if($(this).val().toLowerCase() == p){$(this).prop('disabled',true);$(this).prop('checked',false);}
                            else $(this).prop('disabled',false);
                        });
                    }else{
                        $(this).val('');
                        alert('Please select correct file format');
                    }
                });
                $('button[type=submit]').on('click', function() {
                    $('#result div a').text('Please wait...');
                });
                $('#converter').submit(function( event ) {
                    $('#result div a').text('Please wait...');
                    event.preventDefault();
                    var fileExt = '';
                    $('input[type=radio]').each(function(){
                        if($(this).prop('checked'))fileExt = $(this).val().toLowerCase();
                    });
                    if(fileExt == ''){
                        alert("Chouse converted format!");
                        return false;
                    }
                    if($('input[type=file]').val().length > 2){
                        $('#result').fadeIn(1);
                        var formData = new FormData($('#converter')[0]);
                        $.ajax({
                            type:'POST',
                            url:'{{url('/converter')}}',
                            async:false,
                            data: formData,
                            success:function(data){
                                if(!data.errorStatus)
                                {
                                    dataFile = data.result.split('/');
                                    $('#result div a').attr('href',data.result).attr('download',data.result).text('Please download: ' + dataFile[1]);
                                    console.log(data.result);
                                }else{
                                    $('#result div a').attr('href','#').text('There are some error: ' + data.error);
                                }

                            },
                            cache: false,
                            contentType: false,
                            processData: false
                        });
                    }else alert('Chouse FILE please!');

                    return false;
                });
                function valid(obj, ext){
                    var regex = new RegExp("(.*?)\.(json|xml|csv|yml)$");
                    return regex.test(ext);
                }
            $('.control-label').css('margin-top','20px');
            $('#result a').css('font-size','25px');
            })
        </script>
    </head>
    <body>
    <div class="container text-center">
        <h2>File Format Converter</h2>
        <form class="form-horizontal" id="converter" method="POST" enctype="multipart/form-data" action="">
            {!! csrf_field() !!}
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-6">
                    <label class="control-label" for="source">Chouse You File:</label>
                    <input type="file" class="form-control" id="convertedFile"  name="source">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-6">
                    <h2>Chouse format</h2>
                    <div class="col-sm-3">
                        <label class="control-label">JSON</label>
                        <input type="radio" class="form-control" value="JSON" name="extention">
                    </div>
                    <div class="col-sm-3">
                        <label class="control-label">XML</label>
                        <input type="radio" class="form-control" value="XML" name="extention">
                    </div>
                    <div class="col-sm-3">
                        <label class="control-label">CSV</label>
                        <input type="radio" class="form-control" value="CSV" name="extention">
                    </div>
                    <div class="col-sm-3">
                        <label class="control-label">YML</label>
                        <input type="radio" class="form-control" value="YML" name="extention">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-4 col-sm-4">
                    <button type="submit" class="btn btn-primary col-sm-12">CONVERT</button>
                </div>
            </div>
            <div class="form-group" id="result">
                <div class="col-sm-offset-4 col-sm-4">
                    <a href="#" target="_blank"></a>
                </div>
            </div>
        </form>
    </div>
    </body>
</html>
