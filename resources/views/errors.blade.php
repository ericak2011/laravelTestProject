<x-header/>

<span style="color:red">
    Errors occured or the file is invalid
    <br>
    @foreach ($errors as $err)
        <li>{{$err}}</li>
    @endforeach
</span>

<hr>
<a href="upload_csv">Upload another CSV file</a>
