<!DOCTYPE html>
<html>
<head>
    <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }
        th, td {
            padding: 5px;
            text-align: left;
            vertical-align: top;
        @if(isset($grn->is_international) && $grn->is_international)
            @include('grns.print_international', ['grn' => $grn, 'company_name' => $company_name, 'company_address' => $company_address, 'company_mobile' => $company_mobile, 'company_email' => $company_email])
        @else
            @include('grns.print_local', ['grn' => $grn, 'company_name' => $company_name, 'company_address' => $company_address, 'company_mobile' => $company_mobile, 'company_email' => $company_email])
        @endif
</body>
</html>
