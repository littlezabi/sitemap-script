<style>
    body {
        font-family: 'consolas';
    }
</style>
<?php
$sitemaps_counts = 0;
$total_added = 0;
$xml_url_tags = '';
$write_counts = 1;
$total_urls = 0;

function GenerateSitemapRaw($length, $data_arr, $table)
{

    global $sitemaps_counts, $total_urls, $total_added, $write_counts, $xml_url_tags, $DATE, $xml_url_tags, $ROOT_URL, $PROIRITY, $WRITING_LIMITS;
    // writing methods
    if ($sitemaps_counts == 0) AddStaticURLs();

    $total_urls = $total_urls + $length;
    $writed = $length;
    foreach ($data_arr as $value) {
        $write_counts++;
        $total_added++;
        $writed--;
        $xml_url_tags .= "
    <url>
        <loc>" . handleURLsMethod($value, $table) . "</loc>
        <lastmod>" . $DATE . "</lastmod>
        <priority>" . $PROIRITY . "</priority>
    </url>";
        if ($write_counts > $WRITING_LIMITS) {
            WriteOffFile($xml_url_tags, $table);
            $write_counts = 1;
        }
        if ($writed == 0 && $write_counts != 1) WriteOffFile($xml_url_tags, $table);
    }
}

function WriteOffFile($text, $table = 'project')
{
    global $sitemaps_counts, $xml_url_tags, $ROOT_PATH; {
        $heading = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
        $footing =
            "
</urlset>
        ";
        $text = $heading . $text . $footing;
        $filename = ($sitemaps_counts) == 0 ? 'sitemap_' . $table . '.xml' : 'sitemap' . ($sitemaps_counts) . '_' .  $table . '.xml';
        $fname = $ROOT_PATH . $filename;
        $file = fopen($fname, 'w');
        fwrite($file, $text);
        fclose($file);
        $xml_url_tags = '';
        global $indexSitemapsList;
        $indexSitemapsList = [...$indexSitemapsList, $sitemaps_counts => $filename];
        $sitemaps_counts++;
    }
}

function AddStaticURLs()
{
    global $STATIC_URLS, $total_added, $ROOT_URL, $PROIRITY, $total_urls, $STATIC_URLS, $DATE, $xml_url_tags, $WRITING_LIMITS, $write_counts;
    $total_urls = $total_urls + count($STATIC_URLS);
    foreach ($STATIC_URLS as $url) {
        $write_counts++;
        $total_added++;
        $PROIRITY_ = $url === $ROOT_URL ? $PROIRITY_ = "1.00" : $PROIRITY_ = $PROIRITY;
        $xml_url_tags .= "
    <url>
        <loc>" . $url . "</loc>
        <lastmod>" . $DATE . "</lastmod>
        <priority>" . $PROIRITY_ . "</priority>
    </url>";
    }
}

function CreatingIndexOfSitemaps()
{
    global $indexSitemapsList;
    $heading =
        '<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    $index_text = '';
    global $sitemaps_counts, $ROOT_URL, $ROOT_PATH;
    echo '<hr/><strong>sitemaps links</strong>';
    for ($k = 0; $k < $sitemaps_counts; $k++) {
        $name = '';
        // if ($k == 0) $name = $ROOT_URL . "/sitemaps/sitemap.xml";
        // else $name = $ROOT_URL . "/sitemaps/sitemap" . $k . ".xml";
        $name = $ROOT_URL . 'sitemaps/' . $indexSitemapsList[$k];
        echo '<br/><a href="' . $name . '" target="_blank">' . $name . '</a>';
        $index_text .= "
    <sitemap>
        <loc>" . $name . "</loc>
    </sitemap>";
    }
    echo '<hr/>';
    $footing =
        '
</sitemapindex>';
    $text = $heading . $index_text . $footing;
    try {
        $file = fopen($ROOT_PATH . "sitemap_index.xml", "w");
        fwrite($file, $text);
        fclose($file);
    } catch (Throwable $th) {
        print_r($th);
    }
}

function FetchingData($con, $table, $sql)
{
    if ($table != '') {
        $data = $con->query($sql);
        if ($data->num_rows > 0) {
            $dataset = [];
            while ($row = $data->fetch_assoc()) {
                $dataset[] = $row;
            }
            GenerateSitemapRaw($data->num_rows, $dataset, $table);
        }
    }
}
function PrintDetails()
{
    global $total_added, $ROOT_PATH, $STATIC_URLS, $ROOT_SITEMAP_URL, $sitemaps_counts, $ROOT_URL, $WRITING_LIMITS, $total_urls;
    echo "<strong>";
    echo '<br/>Total URLs: ' . $total_urls;;
    echo '<br/>Total URLs Added: ' . $total_added;
    echo '<br/>URLs Limit in sitemap: ' . $WRITING_LIMITS;
    echo '<br/>Total sitemaps Created: ' . $sitemaps_counts;
    echo '<br/>Root URL is: ' . $ROOT_URL;
    echo '<br/>Web URLs: ';
    logs($STATIC_URLS, $escape  = '<br/>');
    echo '<br/>Index sitemaps link:<a href="' . $ROOT_SITEMAP_URL . 'sitemap_index.xml" target="_blank">' . $ROOT_SITEMAP_URL . 'sitemap_index.xml</a>';
    echo '<br/>Copy the link and paste in your google console to register your sitemaps. <hr/>';
    echo $ROOT_SITEMAP_URL . 'sitemap_index.xml<hr/>';
    echo '<br/>SITEMAPS ROOT FOLDER PATH: <hr/>';
    echo $ROOT_PATH . '<hr/>';
    echo "</strong>";
}

function logs($text_array, $escape = '')
{
    echo '<pre>';
    foreach ($text_array as $text) {
        echo "     ";
        print_r($text);
        echo $escape;
    }
    echo '</pre><br/>';
}

function CreatingSitemapsFolder()
{
    global $ROOT_PATH;
    echo '<strong>';
    echo '<br/>Creating new directory...';
    echo '<br/>directory path: ' . $ROOT_PATH;
    if (!is_dir($ROOT_PATH)) {
        echo '<br/>CREATING NEW FOLDER...';
        if (mkdir($ROOT_PATH)) {
            echo '<br/>FOLDER CREATED FOR STIEMAPS...';
        } else {
            echo '<br/>WARNING: Folder is not created';
        }
    }
    echo '<br/>PROCESSING...WAIT UNTIL DONE!';
    echo '<br/></strong>';
}
