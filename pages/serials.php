<?php
$ip = $licenseServerConfiguration["ip"];
$username = $licenseServerConfiguration["username"];
$password = $licenseServerConfiguration["password"];

mysql_connect($ip, $username, $password);
mysql_select_db("vtiger_module");

$vtigerVersions = array("5.3.0", "5.4.0");
if(!empty($_POST["generate"])) {
    $_POST["h"] = preg_replace("/[^a-zA-Z0-9]/", "", $_POST["h"]);

    $sql = "SELECT * FROM licenses WHERE id = ".intval($_POST["i"])." AND customer_id = '".$_SESSION["cp_user"]["id"]."'";
    $result = mysql_query($sql);
    $data = mysql_fetch_assoc($result);
    if(sha1(md5("abcABC012,-".$data["id"]."#".$data["customer_id"]."#".$data["datum_eintrag"])) == $_POST["h"]) {
        switch($_GET["fkt"]) {
            case "getwebdavserial":
                $licenseKey = base64_encode(substr(sha1(md5($_POST[sha1(md5($_POST["h"]))])), 0, 30));
                $license_for = $_POST[sha1(md5($_POST["h"]))];
                break;
        }
        if(!empty($licenseKey)) {
			$sql = "DELETE FROM licenses WHERE  id = ".intval($_POST["i"])." AND customer_id = '".$_SESSION["cp_user"]["id"]."'";
			mysql_query($sql);
            $sql = "INSERT INTO licenses SET customer_id = '".$_SESSION["cp_user"]["id"]."', max_vtiger_version = 0, extension = '".$data["extension"]."', license_for = '".$license_for."', license_code = '".$licenseKey."'";
            mysql_query($sql);

            header("Location:".CUSTOMER_PORTAL_URL."/serials.html");
            exit();
        }
    }

}
if(!empty($_GET["fkt"])) {
    $_GET["h"] = preg_replace("/[^a-zA-Z0-9]/", "", $_GET["h"]);

    $sql = "SELECT * FROM licenses WHERE id = ".intval($_GET["i"])." AND customer_id = '".$_SESSION["cp_user"]["id"]."'";
    $result = mysql_query($sql);
    $data = mysql_fetch_assoc($result);
    if(sha1(md5("abcABC012,-".$data["id"]."#".$data["customer_id"]."#".$data["datum_eintrag"])) == $_GET["h"]) {
        echo "<div class='serialGenerator'>";
        switch($_GET["fkt"]) {
            case "getwebdavserial":
                    ?><form method='POST' action='#'>
                        <input type="hidden" name="i" value="<?php echo intval($_GET["i"]) ?>">
                        <input type="hidden" name="h" value="<?php echo $_GET["h"] ?>">

                        <div class='serialGenerator'>
                            <p>
                                <label style='display:block;float:left; width:100px;'>Site_Url:&nbsp;<sup>*</sup></label>
                                <input type='text' name="<?php echo sha1(md5($_GET["h"])) ?>" style="width:200px;" value="">
                            </p>
                            <p>
                                <input type="submit" name="generate" style="margin-left:100px;" value="<?php echo T_("Generate license code") ?>">
                            </p>
                        </div>
                        <sup>*</sup> <?php echo T_('Please insert the complete content of your <em><b>$site_Url</b></em> variable from file <em>config.inc.php</em> to generate the correct license.'); ?>
                    </form><?
                break;
        }
        echo "</div>";
    }

}
$sql = "SELECT * FROM licenses WHERE customer_id = '".$_SESSION["cp_user"]["id"]."' ORDER BY extension, datum_eintrag";
$result = mysql_query($sql);

$lastExtension = "";
$availVersions = array();

while($row = mysql_fetch_assoc($result)) {

    if(strtolower($row["extension"]) != $lastExtension) {

        echo "<h2>".$row["extension"]."</h2>";
        $lastExtension = strtolower($row["extension"]);

        $sql = "SELECT vtiger_version FROM extensions WHERE extension = '".$row["extension"]."' GROUP BY vtiger_version";
        $versions = mysql_query($sql);
        #var_dump(mysql_error());
        $availVersions = array();
        while($ver = mysql_fetch_assoc($versions)) {
            $availVersions[] = $ver["vtiger_version"];
        }
    }

    echo "<div class='serialBox clearfix'>";
        echo "<div style='float:left;width:100px;'>".T_("License Code").":</div>";
        if(substr($row["license_code"], 0, 4) == "FKT[") {
            $function = substr($row["license_code"], 4, -1);
            echo "<div style='float:left;width:320px;font-weight:bold;font-family:Courier;'><a href='?fkt=".$function."&i=".$row["id"]."&h=".sha1(md5("abcABC012,-".$row["id"]."#".$row["customer_id"]."#".$row["datum_eintrag"]))."'>".T_("Generate License")."</a></div>";
        } else {
            echo "<div style='float:left;width:320px;font-weight:bold;font-family:Courier;'><span>".$row["license_code"]."</span></div>";
        }
    echo "<div style='float:left;width:100px;'>".T_("purchased on").":</div>";
    echo "<div style='float:left;width:110px;font-weight:bold;font-family:Courier;'>".date("d.m.Y", strtotime($row["datum_eintrag"]))."</div>";

    echo "<br>";

    echo "<div style='float:left;width:100px;'>".T_("License for").":</div>";
    echo "<div style='float:left;width:320px;font-weight:bold;font-family:Courier;'>".htmlentities($row["license_for"])."&nbsp;</div>";

        if($row["max_vtiger_version"] != "0") {
            echo "<div style='float:left;width:100px;'>".T_("updates thru").":</div>";
            if($row["max_vtiger_version"] == "9999") {
                echo "<div style='float:left;width:150px;font-weight:bold;font-family:Courier;'>UNLIMITED</div>";
            } else {
                echo "<div style='float:left;width:150px;font-weight:bold;font-family:Courier;'>vtigerCRM Version ".$row["max_vtiger_version"]."</div>";
            }
        }
    echo "<br>";

   	echo "<div style='float:left;width:250px;margin-top:5px;'>".T_("Download Extension").":";
    foreach($vtigerVersions as $version) {
        $versionString = preg_replace("/[^0-9a-z]/","", $version);

        if(!in_array($versionString, $availVersions))
           continue;

        if($versionString > $row["max_vtiger_version"] && $row["max_vtiger_version"] > "0")
            break;

        echo "<a style='margin:0 10px;' href='http://vtiger.stefanwarnat.de/extensions/download.php?vtiger_version=".$versionString."&extension=".$row["extension"]."&license=".md5($row["license_code"])."'>".$version."</a>";
    }
    echo "</div>";

    echo "</div>";

}