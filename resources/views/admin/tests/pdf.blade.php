<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <style type="text/css">
        table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            margin: 0 auto;
        }
        table td {
            padding:5px;
            vertical-align:top;
        }
        body, .body {
            width:1000px;
            margin:20px auto;
            padding:30px;
            font-size:16px;
            line-height:24px;
            font-family:'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color:#555;
        }
        table tr td:nth-child(2){
            text-align:right;
        }

        table tr.top table td{
            padding-bottom:20px;
        }

        table tr.top table td.title{
            font-size:45px;
            line-height:45px;
            color:#333;
        }

        table tr.information table td{
            padding-bottom:40px;
        }

        table tr.heading td{
            background:#eee;
            border-bottom:1px solid #ddd;
            font-weight:bold;
        }

        table tr.details td{
            padding-bottom:20px;
        }

        table tr.item td{
            border-bottom:1px solid #eee;
        }

        table tr.item.last td{
            border-bottom:none;
        }

        table tr.total td:nth-child(2){
            border-top:2px solid #eee;
            font-weight:bold;
        }
    </style>
</head>
<body>
<div class="body">
    <table cellpadding="0" cellspacing="0">
        <tbody>
        <tr class="information">
            <td colspan="2">
                <table>
                    <tbody>
                    <tr>
                        <td style="text-align: center">
                            <b>{{ $course->title }}</b><br>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>

        <tr class="heading">
            <td>
                {{ $test->title }}
            </td>
        </tr>
        <tr>
            <td>
            </td>
        </tr>
        </tbody>
    </table>
    @include('admin.tests.partials.results', compact('test'))
</div>
</body>
</html>
