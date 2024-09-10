<?php
require_once('connections/config.php');
require_once('components/misc.inc.php');

$target_url_name = '';
$domain_to_use = ROOT_DIR;
$prevent_too_many_links = true;

header("Content-type: application/xml");
echo '<?xml version="1.0" encoding="UTF-8"?>';

if (!$_GET['index']) {
    echo '<urlset><url><loc></loc></url></urlset>';
    exit();
}

if ($_GET['index'] != 'all') {
    $batch_index = $_GET['index'] - 1;
}

if ($site_version == 'localhost') {
    $limit = 1000; 
}
else {
    $limit = 49990;
}

$siteUrl = [
    "about-lrea", "about-lrea/our-story", "about-lrea/our-mission", "about-lrea/culture-and-structure", "about-lrea/education", "about-lrea/our-vision", "about-lrea/our-values", 
    "about-lrea/learning-model", "about-lrea/accreditation", "subject/business-management", "subject/health-and-psychology", "subject/business-law", "subject/information-technology", "subject/personal-education", "subject/teaching-and-education", "subject/health-and-safety", "subject/finance-and-accounting",
    "subject/office-productivity", "subject/sales-and-marketing", "subject/leadership-and-entrepreneurship", "subject/human-resources", "category/masters-degrees",
    "category/bachelors-degrees", "category/short-courses", "category/advanced-professional-certificate-courses",
    "learning-with-lrea", "learning-with-lrea/applying-to-lrea", "learning-with-lrea/refund-policy", "terms/study-contract", "terms", "terms/accessibility-policy", "terms/admission-policy", "faq"
];

mysqli_select_db($config, $database_config);
$query_DisProg = "SELECT np.*, oprel.option_two, op.option_value  
                FROM option_relationships oprel 
                JOIN options op ON oprel.option_two = op.option_id 
                JOIN new_programmes np ON oprel.option_one  = np.programme_id 
                WHERE oprel.relationship_name = 'programme type'
                    AND np.programme_status = 'active'
                    AND np.programme_id NOT IN (
                        SELECT np.programme_id  
                        FROM option_relationships option_prog_category 
                        JOIN options op_category ON option_prog_category.option_two = op_category.option_id 
                        JOIN new_programmes np ON option_prog_category.option_one  = np.programme_id 
                        WHERE option_prog_category.relationship_name = 'programme category' 
                            AND op_category.option_value = 'training' 
                            AND np.deleted = '0'
                    )
                    AND np.deleted = '0'";
$DisProg = mysqli_query($config, $query_DisProg) or die(mysqli_error($config));
$row_DisProg = mysqli_fetch_assoc($DisProg);
$totalRows_DisProg = mysqli_num_rows($DisProg);

if ($totalRows_DisProg) {
    do { 
        $programmesSiteUrl[] = "programme/".$row_DisProg['url_name'];
    } while ($row_DisProg = mysqli_fetch_assoc($DisProg));
}

$all_links = array();
$serial_no = 0;

if ($_GET['index'] != 'simple') {
    // Get the different countries and states
    mysqli_select_db($config, $database_config);
    if ($prevent_too_many_links) {
        $query_Locations = "SELECT IF (url_name = 'united-kingdom', '', url_name) as url_name
                            FROM myip_ms_countries
                            WHERE url_name IN ('united-kingdom', 'nigeria', 'uae', 'usa')";
    }
    else {
        $query_Locations = "(SELECT IF (url_name = 'united-kingdom', '', url_name) as url_name
                            FROM myip_ms_countries)
                            UNION 
                            (SELECT url_name 
                            FROM myip_ms_states)";
    }
    $Locations = mysqli_query($config, $query_Locations) or die(mysqli_error($config));
    $row_Locations = mysqli_fetch_assoc($Locations);
    $totalRows_Locations = mysqli_num_rows($Locations);

    if ($totalRows_Locations) {
        // All states and countries homepages
        do {
            if ($prevent_too_many_links) {
                if ($target_url_name == $row_Locations['url_name']) {
                    $all_links[$serial_no]['url'] = $domain_to_use;

                    $all_links[$serial_no]['lastmod'] = $date_now;
                    $all_links[$serial_no]['changefreq'] = 'daily';
                    $all_links[$serial_no]['priority'] = '1';
                }
            }
            else {
                $all_links[$serial_no]['url'] = ROOT_DIR.$row_Locations['url_name'];
                
                if (in_array($row_Locations['url_name'], array('', 'uae', 'nigeria', 'usa'))) {
                    $all_links[$serial_no]['lastmod'] = $date_now;
                    $all_links[$serial_no]['changefreq'] = 'daily';
                    $all_links[$serial_no]['priority'] = '1';
                }
                else {
                    $all_links[$serial_no]['changefreq'] = 'weekly';
                    $all_links[$serial_no]['priority'] = '0.5';
                }
            }

            $serial_no++;
        } while($row_Locations = mysqli_fetch_assoc($Locations));
        // Then reset the pointer to the beginning
        $rows = mysqli_num_rows($Locations); if($rows > 0) { mysqli_data_seek($Locations, 0); $row_Locations = mysqli_fetch_assoc($Locations); }
        
        //echo '<urlset>';

        // All states and countries inner pages
        do {
            if ($prevent_too_many_links) {
                if ($target_url_name == $row_Locations['url_name']) {
                    foreach ($siteUrl as $webaddr) {
                        $all_links[$serial_no]['url'] = $domain_to_use.$webaddr;

                        $all_links[$serial_no]['lastmod'] = $date_now;
                        $all_links[$serial_no]['changefreq'] = 'weekly'; // daily
                        $all_links[$serial_no]['priority'] = '0.5';

                        $serial_no++;
                    }
                }
            }
            else {
                foreach ($siteUrl as $webaddr) {
                    $all_links[$serial_no]['url'] = ROOT_DIR.$row_Locations['url_name'].'/'.$webaddr;

                    if (in_array($row_Locations['url_name'], array('', 'uae', 'nigeria', 'usa'))) {
                        $all_links[$serial_no]['lastmod'] = $date_now;
                        $all_links[$serial_no]['changefreq'] = 'weekly'; // daily
                        $all_links[$serial_no]['priority'] = '0.5'; // 1
                    }
                    else {
                        $all_links[$serial_no]['changefreq'] = 'monthly'; // weekly
                        $all_links[$serial_no]['priority'] = '0.5';
                    }

                    $serial_no++;
                }
            }
            //echo '<url><loc>'.ROOT_DIR.$row_Locations['url_name'].'</loc></url>';
        } while($row_Locations = mysqli_fetch_assoc($Locations));
        //echo '</urlset>';
        // Then reset the pointer to the beginning
        $rows = mysqli_num_rows($Locations); if($rows > 0) { mysqli_data_seek($Locations, 0); $row_Locations = mysqli_fetch_assoc($Locations); }
        
        //echo '<urlset>';

        // All courses for uae, nigeria and uk inner pages
        do {
            if ($prevent_too_many_links) {
                if ($target_url_name == $row_Locations['url_name']) {
                    foreach ($programmesSiteUrl as $progaddr) {
                        $all_links[$serial_no]['url'] = $domain_to_use.$progaddr;
                        $all_links[$serial_no]['changefreq'] = 'monthly';
                        $all_links[$serial_no]['priority'] = '0.5';

                        $serial_no++;
                    }
                }
            }
            else {
                if (in_array($row_Locations['url_name'], array('', 'uae', 'nigeria', 'usa'))) {
                    foreach ($programmesSiteUrl as $progaddr) {

                        if ($row_Locations['url_name'] != "") {
                            $add_slash_to_url = "/";
                        }
                        else {
                            $add_slash_to_url = "";
                        }

                        $all_links[$serial_no]['url'] = ROOT_DIR.$row_Locations['url_name'].$add_slash_to_url.$progaddr;
                        $all_links[$serial_no]['changefreq'] = 'monthly'; // weekly
                        $all_links[$serial_no]['priority'] = '0.5';

                        $serial_no++;
                    }
                }
            }
            //echo '<url><loc>'.ROOT_DIR.$row_Locations['url_name'].'</loc></url>';
        } while($row_Locations = mysqli_fetch_assoc($Locations));
        //echo '</urlset>';
        // Then reset the pointer to the beginning
        $rows = mysqli_num_rows($Locations); if($rows > 0) { mysqli_data_seek($Locations, 0); $row_Locations = mysqli_fetch_assoc($Locations); }
    }
    else {
        exit('<urlset><url><loc></loc></url></urlset>');
    }
}
else {
    $all_links[$serial_no]['url'] = ROOT_DIR;
    
    $all_links[$serial_no]['lastmod'] = $date_now;
    $all_links[$serial_no]['changefreq'] = 'daily';
    $all_links[$serial_no]['priority'] = '1';

    $serial_no++;

    foreach ($siteUrl as $webaddr) {
        $all_links[$serial_no]['url'] = ROOT_DIR.$webaddr;

        $all_links[$serial_no]['lastmod'] = $date_now;
        $all_links[$serial_no]['changefreq'] = 'daily';
        $all_links[$serial_no]['priority'] = '1';
        $serial_no++;
    }
}

// echo count($siteUrl)."<br>";
// echo $totalRows_Locations."<br>";
// echo $totalRows_Locations * count($siteUrl)."<br>";
// echo count($all_links)."<br>";

if (count($all_links)) {
    $batches = array_chunk($all_links, $limit);
    //echo count($batches)."<br>";

    //echo '<pre>'; var_dump($batches[3]); echo '</pre>';
}

//exit();
?>

<?php //<urlset xmlns="http://www.google.com/schemas/sitemap/0.84" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.google.com/schemas/sitemap/0.84 http://www.google.com/schemas/sitemap/0.84/sitemap.xsd"> ?>

<?php if ($_GET['index'] == 'simple') { // Not in use at the moment ?>
    <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
        <?php if (count($batches[0])) {?>
            <?php foreach ($batches[0] as $this_link) { // loop through the links  ?>            
                <url>
                    <loc><?php echo $this_link['url']; ?></loc>
                    <?php if ($this_link['lastmod']) { ?>
                        <lastmod><?php echo $this_link['lastmod']; ?></lastmod>
                    <?php } ?>
                    <?php if ($this_link['changefreq']) { ?>
                        <changefreq><?php echo $this_link['changefreq']; ?></changefreq>
                    <?php } ?>
                    <?php if ($this_link['priority']) { ?>
                        <priority><?php echo $this_link['priority']; ?></priority>
                    <?php } ?>
                </url>
            <?php } ?>
        <?php } else { ?>
            <url><loc></loc></url>
        <?php } ?>
    </urlset>
<?php } else { // Actual one being used now ?>
    <?php if ($_GET['index'] != 'all') { ?>
        <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
            <?php if (count($batches[$batch_index])) {?>
                <?php foreach ($batches[$batch_index] as $this_link) { // loop through the links  ?>            
                    <url>
                        <loc><?php echo $this_link['url']; ?></loc>
                        <?php if ($this_link['lastmod']) { ?>
                            <lastmod><?php echo $this_link['lastmod']; ?></lastmod>
                        <?php } ?>
                        <?php if ($this_link['changefreq']) { ?>
                            <changefreq><?php echo $this_link['changefreq']; ?></changefreq>
                        <?php } ?>
                        <?php if ($this_link['priority']) { ?>
                            <priority><?php echo $this_link['priority']; ?></priority>
                        <?php } ?>
                    </url>
                <?php } ?>
            <?php } else { ?>
                <url><loc></loc></url>
            <?php } ?>
        </urlset>
    <?php } else { ?>
        <sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
            <?php if (count($batches)) { ?>
                <?php for ($i = 1; $i <= count($batches); $i++) { ?>
                    <sitemap>
                        <loc><?php echo ROOT_DIR.'sitemap-'.$i.'.xml'; ?></loc>
                        <lastmod><?php echo date("Y-m-d\T01:37:00+01:00"); ?></lastmod>
                    </sitemap>
                <?php } ?>
            <?php } else { ?>
                <sitemap><loc></loc></sitemap>
            <?php } ?>
        </sitemapindex>
    <?php } ?>
<?php } ?>