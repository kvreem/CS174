<?php
namespace Controllers;

class Chart extends Base {
    
    // this function restructures the data retrieved from database
    // this is the form in the database:
    // array(
    //      array(label, value1, value2),
    //      array(label for 2nd row, value1, value2)
    // )
    // and this is what we need:
    // array(
    //      label => value1,
    //      label for 2nd row => value2,
    //      etc...
    // )
    
    protected function get_data_series($original) {
        $parsed = array();
        
        foreach ($original as $row) {

            // the first element is label, others are points
            $parsed[$row[0]] = array_slice($row, 1);
        }
        
        return json_encode($parsed);
    }
    
    function index() {
        $hash = $_GET['arg2'];
        $chartdata = new \Models\ChartDataRow();
        $data = $chartdata->load_data($hash);
        if (!$data) {
            // chart not found
            // redirect to landing page
            header("Location: ?c=landing");
            return;
        }
        $data['hash'] = $hash;
        $type = $_GET['arg1'];
       
        if ($type == 'LineGraph' || $type == 'PointGraph' || $type == 'Histogram' || $type == 'xml') {
            $data['type'] = $type;
            $this->renderPage($data);
        }
        elseif ($type == 'json') {
            header('Content-Type: application/json');
            echo $this->get_data_series($data['data']);
        }
        elseif ($type == 'jsonp') {
            header('Content-Type: application/javascript');
            $callback = empty($_GET['arg3']) ? 'alert' : $_GET['arg3'];
            echo $callback . '(';
            echo $this->get_data_series($data['data']);
            echo ');\n';
        }
    }
    
    // if an html page is requested:
    function renderPage($data) {
        $view = new \Views\Chart();
        // link url base
        $link_url = \Configs\Config::BASE_URL . '?c=chart&amp;a=show&amp;arg2=' . $data["hash"] . '&amp;arg1=';
        $share_links = array(
            'LineGraph' => $link_url . 'LineGraph',
            'PointGraph' => $link_url . 'PointGraph',
            'Histogram' => $link_url . 'Histogram',
            'xml' => $link_url . 'xml',
            'json' => $link_url . 'json',
            'jsonp' => $link_url . 'jsonp'
        );
        // get the page title and pass it into the current view 
        $template_vars = array(
            "site_title" => $data['hash'] . " Line Graph - PasteChart",
            "title" => $data['title'],
            "json" => $this->get_data_series($data['data'], 1),
            "links" => $share_links,
            "type" => $data["type"],
            "hash" => $data["hash"],
            "data" => $data["data"]
        );
        
        if ($data['type'] == 'xml') {
             $view->render_xml($template_vars);
        } else {
             $view->render($template_vars);
        }
    }
}