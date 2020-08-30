<?php

namespace App\Http\Controllers;

use Hamcrest\Core\IsNull;
use Hamcrest\Type\IsNumeric;
use Illuminate\Http\Request;
use League\CommonMark\Block\Element\IndentedCode;

define("MIN_COL_COUNT", 3);
define("MAX_COL_COUNT", 10);

class Upload extends Controller
{
    // resolution of column data type, based on a particular value
    private function resolve_data_type($value)
    {
        return (is_numeric($value) ? 'number': 'string');
    }

    // checking of csv data values consistency
    private function verify_csv_data(&$errors, &$col_count, &$col_types, &$csv_data)
    {
        foreach ($csv_data as $line=>$data_row) {
            // make sure that number of columns in each data row matches the headers
            if (count($data_row) < $col_count) {
                array_push($errors, 'Not enough values on line ' . ($line+2));
            }
            elseif (count($data_row) > $col_count) {
                array_push($errors, 'Too many values on line ' . ($line+2));
            }
            else {
                // make sure that data types of values are the same in each data row
                foreach ($data_row as $col=>$value) {
                    if ($col_types[$col] != $this->resolve_data_type($value)) {
                        array_push($errors, 'Unmatching data type of value ' . ($col+1) . ' on line ' . ($line+2));
                    }
                }
            }
        }
    }

    // actual parsing of csv file contents
    private function process_csv_data(&$data)
    {
        $errors = [];
        if (count($data) == 0) {
            array_push($errors, 'The file is empty');
        }
        else {
            // colunms count (resolution based on headers row)
            $col_count = count($data[0]);
            if ($col_count < MIN_COL_COUNT) {
                array_push($errors, 'Not enough columns, required min ' . MIN_COL_COUNT);
            }
            elseif ($col_count > MAX_COL_COUNT) {
                array_push($errors, 'Too many columns, required max ' . MAX_COL_COUNT);
            }
            // header fields
            $csv_col_headers = [];
            foreach ($data[0] as $col=>$value) {
                $csv_col_headers[$col] = $value;
            }
            // data values
            $csv_data = array_slice($data, 1);
            // data types (resolution based on first data row)
            $csv_col_types = [];
            foreach ($csv_data[0] as $col=>$value) {
                $csv_col_types[$col] = $this->resolve_data_type($value);
            }
            // check data consistency to prevent errors later
            $this->verify_csv_data($errors, $col_count, $csv_col_types, $csv_data);
            // original raw csv in json
            $csv_json = json_encode($data);
        }

        // display errors if there are any
        if (count($errors) > 0) {
            return view('errors', compact('errors'));
        }
        // display the contents
        return view('display',
            compact('csv_col_headers', 'csv_col_types', 'csv_data', 'csv_json')
        );
    }


    // Handler for 'Upload File' buttom
    public function upload_file(Request $req)
    {
        // upload the csv file
        $file = $req->file('csv');
        $path = $file->store('uploads');

        // parse the csv file
        $real_path = $req->file('csv')->getRealPath();
        $data = array_map('str_getcsv', file($real_path));

        return $this->process_csv_data($data);
    }


    // Handler for 'Add Column' button
    public function add_column(Request $req)
    {
        $data = json_decode($req->csv_json, true);

        // add new column as combination of 1st and 3rd columns
        array_push($data[0], 'New Column');
        for ($i=1; $i<count($data); $i++) {
            $value1 = $data[$i][0];
            $value2 = $data[$i][2];
            if (is_numeric($value1) && is_numeric($value2)) {
                // if both are numbers, make a sum
                $new_value = $value1 + $value2;
            }
            else {
                // otherwise just concatenate strings
                $new_value = $data[$i][0] . $data[$i][2];
            }
            array_push($data[$i], $new_value);
        }

        return $this->process_csv_data($data);
    }
}
