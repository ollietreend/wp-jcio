<?php

namespace Scraper\Source\ContentEntity;

use Scraper\Source\Resource;
use Scraper\Utility\LazyProperties;
use Smalot\PdfParser\Parser;

class DisciplinaryStatementEntity
{
    use LazyProperties;

    /**
     * Define lazy properties for the class
     *
     * @var array
     */
    private $lazyProperties = [
        'title',
        'date',
        'filePath',
    ];

    /**
     * Associated document resource
     *
     * @var Resource
     */
    public $resource = null;

    /**
     * Statement title
     *
     * @var string
     */
    private $title = null;

    /**
     * Statement year
     *
     * @var int
     */
    private $year = null;

    public function __construct($title, $year, Resource $resource)
    {
        $this->resource = $resource;
        $this->title = $title;
        $this->year = $year;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getFilePath()
    {
        return $this->resource->getFilePath();
    }

    public function getDate()
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($this->filePath);
        $text = $pdf->getText();

        // Try and find a date in the PDF text
        $found = preg_match('/(\d+)([a-z]{2})?\s(January|February|March|April|May|June|July|August|September|October|November|December)\s(\d{4})/i', $text, $matches);

        $regexFullDate = '/(\d+)([a-z]{2})?\s(January|February|March|April|May|June|July|August|September|October|November|December)\s(\d{4})/i';
        $regexMonthYear = '/(January|February|March|April|May|June|July|August|September|October|November|December)\s(\d{4})/i';

        if (preg_match($regexFullDate, $text, $matches)) {
            // We found the full date (DMY) in the statement text

            $date = join(' ', [$matches[1], $matches[3], $matches[4]]);
            $date = new \DateTime($date);
            return $date;
        } else if (preg_match($regexMonthYear, $text, $matches)) {
            // We only found the month & year (MY) in the statement text

            $date = join(' ', [$matches[1], $matches[2]]);

            // Take the day from the PDF meta data, and month/year from the statement text
            $details = $pdf->getDetails();
            $pdfDate = new \DateTime($details['CreationDate']);

            $date = $pdfDate->format('d ') . $date;
            $date = new \DateTime($date);
            return $date;
        } else {
            trigger_error('Unable to find date in statement: "' . $this->title . '" with file: ' . $this->filePath, E_USER_NOTICE);
            return new \DateTime($this->year);
        }
    }
}
