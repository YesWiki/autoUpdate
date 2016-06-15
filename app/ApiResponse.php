<?php
namespace AutoUpdate;


class ApiResponse
{
    /**
     * Html response code
     * @var integer
     */
    private $htmlCode;

    /**
     * Data sent in response
     * @var [type]
     */
    private $data;

    /**
     * Constructor
     * @param integer $htmlCode response code
     * @param array   $data     data sent in response
     */
    public function __construct($htmlCode = 200, $data = array())
    {
        $this->setHtmlResponseCode($htmlCode);
        $this->setData($data);
    }

    /**
     * Modify the html response code.
     * @param integer $htmlCode Html response code
     */
    public function setHtmlResponseCode($htmlCode)
    {
        $this->htmlCode = $htmlCode;
    }


    /**
     * Define data sent back
     * @param array $data data sent back
     */
    public function setData($data)
    {
        $this->data = json_encode($data);
    }

    /**
     * Shown data and set http response code. The script should be terminate
     * after.
     *
     * @return void
     */
    public function send()
    {
        print($this->data);
        http_response_code($this->htmlCode);
    }
}
