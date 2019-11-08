<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{$certTitle}}</title>
	<style type="text/css">
        *,:after,:before{-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box}
        body{background:#FFF;font-family:CAMBRIA;font-size:20px}
        {{$certStyle}}
	</style>
    </head>
    <body>
        <div class="crbox" style="background-image: url({{ $background }})">
            <div class="cr-logo">
                <img src="{{ $crLogo }}" width="100%" style="{{ $crLogoStyle }}"/>
            </div>
            <div class="cr-border">
                <img src="{{ $crBorder }}" width="100%" style="{{ $crBorderStyle }}"/>
            </div>
            {!! $body !!}
            <div></div>
            <table width="100%">
                <tr>
                    <td align="left"><div class="cr-sign" style="text-align:left;"><img src="{{ $crSign }}" width="100%" style="{{ $crSignStyle }}"/></div></td>
                    <td align="right"><div class="cr-badge" style="text-align:right;"><img src="{{ $crBadge }}" width="100%" style="{{ $crBadgeStyle }}"/></div></td>
                </tr>
            </table>
        </div>
    </body>
</html>