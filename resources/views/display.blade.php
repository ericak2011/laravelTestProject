<x-header/>

<form action="add_column" method="post">
    <input type="hidden" name="csv_json" value="{{ $csv_json }}"/>

    <table>
        {{-- headers & data types --}}
        <tr>
            @foreach ($csv_col_headers as $key=>$header_field)
                <th>{{ $header_field }} : {{ $csv_col_types[$key] }}</th>
            @endforeach
        </tr>

        {{-- data rows --}}
        @foreach ($csv_data as $row)
            <tr>
                @foreach ($row as $key=>$value)
                    <td>{{ ((strlen(ltrim($value))==0 || ltrim($value)=="0") ? '[EMPTY]' : $value) }}</td>
                @endforeach
            </tr>
        @endforeach

    </table>

    <br>
    @csrf
    <p>
        Add a new column that combines the values of first and third columns and add it to the end of the table
    </p>
    <button type="submit">Add New Column</button>
    <br>
</form>

<hr>
<a href="upload_csv">Upload another CSV file</a>
