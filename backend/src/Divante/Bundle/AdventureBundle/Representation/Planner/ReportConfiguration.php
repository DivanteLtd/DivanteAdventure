<?php
namespace Divante\Bundle\AdventureBundle\Representation\Planner;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\Filesystem\Filesystem;

class ReportConfiguration
{
    protected Spreadsheet $excel;
    protected Worksheet $activeSheet;
    /** @var array<int|string,mixed> */
    protected array $structure;

    public function __construct()
    {
        $this->excel = new Spreadsheet();
    }

    /**
     * @param array<int|string,mixed> $structure
     */
    public function setStructure(array $structure): void
    {
        $this->structure = $structure;
    }

    /**
     * @param string $filename
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function generateReport(string $filename) :void
    {
        $this
            ->setProperties()
            ->setActiveSheet()
            ->setHeaders()
            ->setData()
            ->saveReport($filename);
    }

    protected function setHeaders() :self
    {
        $this->activeSheet->setCellValueExplicitByColumnAndRow(
            1,
            1,
            'Pracownik',
            DataType::TYPE_STRING
        );
        $this->activeSheet->getColumnDimensionByColumn(1)->setAutoSize(true);
        $days = $this->explodeDaysFromStructure();
        foreach ($days as $key => $val) {
            if ($this->isWeekend($val)) {
                $val = '';
                $this->setBackgroundColorByColumnAndRow($key + 2, 1);
            }
            $this->activeSheet->setCellValueExplicitByColumnAndRow(
                $key + 2,
                1,
                $val,
                DataType::TYPE_STRING
            );
            $this->activeSheet->getColumnDimensionByColumn($key + 2)->setAutoSize(true);
        }
        return $this;
    }

    protected function setData() :self
    {
        $row = 2;
        foreach ($this->structure as $key => $item) {
            $this->activeSheet->setCellValueExplicitByColumnAndRow(
                1,
                $row,
                $item['fullName'],
                DataType::TYPE_STRING
            );
            $this->activeSheet->getStyle('A' . $row)->getFont()->setBold(true);
            $column = 2;
            foreach ($item['days'] as $i => $day) {
                $value = '0';
                if (!is_null($day['occupancy'])) {
                    $value = sprintf('%0.0f', $day['occupancy']);
                }
                if ($day['freeDay'] !== false) {
                    $value = 'Dzień wolny';
                }
                if ($this->isWeekend($i)) {
                    $value = '';
                    $this->setBackgroundColorByColumnAndRow($column, $row);
                }
                $this->activeSheet->setCellValueExplicitByColumnAndRow(
                    $column,
                    $row,
                    $value,
                    DataType::TYPE_STRING
                );
                $this->activeSheet->getStyle("$row:$row")->getFont()->setBold(true);

                $column++;
            }
            foreach ($item['items'] as $project) {
                $row++;
                $column = 2;
                $this->activeSheet->setCellValueExplicitByColumnAndRow(
                    1,
                    $row,
                    $project['name'],
                    DataType::TYPE_STRING
                );
                foreach ($project['days'] as $j => $day) {
                    $value = '0';
                    if (!is_null($day['occupancy'])) {
                        $value = sprintf('%0.0f', $day['occupancy']);
                    }
                    if ($day['freeDay'] !== false) {
                        $value = 'Dzień wolny';
                    }
                    if ($this->isWeekend($j)) {
                        $value = '';
                        $this->setBackgroundColorByColumnAndRow($column, $row);
                    }
                    $this->activeSheet->setCellValueExplicitByColumnAndRow(
                        $column,
                        $row,
                        $value,
                        DataType::TYPE_STRING
                    );
                    $column++;
                }
            }
            $row++;
        }
        return $this;
    }

    /** @return array<int|string,mixed> */
    protected function explodeDaysFromStructure() :array
    {
        $days = [];
        if (isset($this->structure[0]['days'])) {
            $days = array_keys($this->structure[0]['days']);
        }
        return $days;
    }

    protected function setProperties() :self
    {
        $this->excel->getProperties()
            ->setCreator("Adventure - Open HRM")
            ->setLastModifiedBy("Adventure Team")
            ->setTitle("Raport z plannera")
            ->setSubject("Raport z plannera")
            ->setDescription("Raport z plannera");

        return $this;
    }

    /**
     * @return ReportConfiguration
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    protected function setActiveSheet() :self
    {
        $this->activeSheet = $this->excel->getActiveSheet();
        $this->activeSheet->setTitle('Raport z plannera');

        return $this;
    }

    /**
     * @param string $filename
     * @return ReportConfiguration
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    protected function saveReport(string $filename) :self
    {
        $path = pathinfo($filename, PATHINFO_DIRNAME);
        $this->createFolderIfNeeded($path);
        $writer = new Xlsx($this->excel);
        $writer->save($filename);

        return $this;
    }

    protected function createFolderIfNeeded(string $path) :void
    {
        $filesystem = new Filesystem();
        if (!$filesystem->exists($path)) {
            $filesystem->mkdir($path);
        }
    }

    protected function isWeekend(string $date) :bool
    {
        $inputDate = \DateTime::createFromFormat("Y-m-d", $date, new \DateTimeZone("Europe/Warsaw"));
        return $inputDate->format('N') >= 6;
    }

    protected function setBackgroundColorByColumnAndRow(int $col, int $row) :void
    {
        $cell = $this->activeSheet->getCellByColumnAndRow($col, $row);
        $this->activeSheet->getStyle($cell->getCoordinate())
                          ->getFill()
                          ->setFillType(Fill::FILL_SOLID)
                          ->getStartColor()->setARGB('D1D1D1');
    }

    protected function loop(int $h): void
    {
        for ($r = 2; $r <= $h; $r++) {
            $value = $this->activeSheet->getCellByColumnAndRow(1, $r)->getValue();
            if ($value == null) {
                $this->activeSheet->removeRow($r);
                break;
            }
        }
    }
}
