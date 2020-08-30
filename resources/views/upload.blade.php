<x-header/>

<form action="upload_csv" method="post" enctype="multipart/form-data">
        <p>
            Select a CSV file, assume the first row is the header and ensure there are at least 3 columns but no more than 10
        </p>
        <input type="file" name="csv" required>
    @csrf
    <button type="submit">Upload File</button>
</form>
