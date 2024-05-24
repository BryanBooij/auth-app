<?php
require_once 'vendor/autoload.php';

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style/stylesheet.css">
    <title>Number</title>
</head>
<body>
<center>
    <img src="img/lock.png" width="200" height="200"><br><br>
    <form action="<?php echo route('number_validation');?>" method="post" id="phoneForm">
        <label for="country">Country:</label>
        <select id="country" name="country" onchange="updateCountryCode()">
            <option value="+31">🇳🇱 (+31) Netherlands</option>
            <option value="+44">🇬🇧 (+44) United Kingdom</option>
            <option value="+34">🇪🇸 (+34) Spain</option>
            <option value="+49">🇩🇪 (+49) Germany</option>
            <option value="+33">🇫🇷 (+33) France</option>
            <option value="+351">🇵🇹 (+351) Portugal</option>
            <option value="+39">🇮🇹 (+39) Italy</option>
            <option value="+1">🇺🇸 (+1) United States</option>
            <option value="+355">🇦🇱 (+355) Albania</option>
            <option value="+376">🇦🇩 (+376) Andorra</option>
            <option value="+374">🇦🇲 (+374) Armenia</option>
            <option value="+43">🇦🇹 (+43) Austria</option>
            <option value="+994">🇦🇿 (+994) Azerbaijan</option>
            <option value="+375">🇧🇾 (+375) Belarus</option>
            <option value="+32">🇧🇪 (+32) Belgium</option>
            <option value="+387">🇧🇦 (+387) Bosnia and Herzegovina</option>
            <option value="+359">🇧🇬 (+359) Bulgaria</option>
            <option value="+385">🇭🇷 (+385) Croatia</option>
            <option value="+357">🇨🇾 (+357) Cyprus</option>
            <option value="+420">🇨🇿 (+420) Czech Republic</option>
            <option value="+45">🇩🇰 (+45) Denmark</option>
            <option value="+372">🇪🇪 (+372) Estonia</option>
            <option value="+298">🇫🇴 (+298) Faroe Islands</option>
            <option value="+358">🇫🇮 (+358) Finland</option>
            <option value="+995">🇬🇪 (+995) Georgia</option>
            <option value="+350">🇬🇮 (+350) Gibraltar</option>
            <option value="+30">🇬🇷 (+30) Greece</option>
            <option value="+299">🇬🇱 (+299) Greenland</option>
            <option value="+36">🇭🇺 (+36) Hungary</option>
            <option value="+354">🇮🇸 (+354) Iceland</option>
            <option value="+353">🇮🇪 (+353) Ireland</option>
            <option value="+377">🇲🇨 (+377) Monaco</option>
            <option value="+370">🇱🇹 (+370) Lithuania</option>
            <option value="+352">🇱🇺 (+352) Luxembourg</option>
            <option value="+389">🇲🇰 (+389) North Macedonia</option>
            <option value="+356">🇲🇹 (+356) Malta</option>
            <option value="+373">🇲🇩 (+373) Moldova</option>
            <option value="+377">🇲🇨 (+377) Monaco</option>
            <option value="+382">🇲🇪 (+382) Montenegro</option>
            <option value="+47">🇳🇴 (+47) Norway</option>
            <option value="+48">🇵🇱 (+48) Poland</option>
            <option value="+40">🇷🇴 (+40) Romania</option>
            <option value="+7">🇷🇺 (+7) Russia</option>
            <option value="+378">🇸🇲 (+378) San Marino</option>
            <option value="+381">🇷🇸 (+381) Serbia</option>
            <option value="+421">🇸🇰 (+421) Slovakia</option>
            <option value="+386">🇸🇮 (+386) Slovenia</option>
            <option value="+46">🇸🇪 (+46) Sweden</option>
            <option value="+41">🇨🇭 (+41) Switzerland</option>
            <option value="+90">🇹🇷 (+90) Turkey</option>
            <option value="+380">🇺🇦 (+380) Ukraine</option>
            <option value="+379">🇻🇦 (+379) Vatican City</option>
            <option value="">() Different country</option>
        </select><br>
        <label for="phone">Phone Number:</label>
        <label for="countryCode"></label>
        <input type="text" id="countryCode" name="countryCode" value="+31" size="1%" readonly> <!-- readonly for users to better understand the purpose -->
        <input type="tel" id="phone" name="phone" placeholder="612345678" minlength="8" required><br>
        <button type="submit">Submit</button>
    </form>
</center>
<?php
if (session('error_number')) {
    echo '<center><p style="color: red;">' . session('error_number') . '</center></p>';
    //session('error');
}
?>

<script>
    // function for updating countryCode value
    function updateCountryCode() {
        document.getElementById("countryCode").value = document.getElementById("country").value;
    }
</script>

</body>
</html>
