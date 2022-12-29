<?php

namespace XiDanko\Jasper;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use XiDanko\Jasper\Exceptions\JasperStarterNotFoundException;
use XiDanko\Jasper\Exceptions\ParameterDoesntExistException;
use XiDanko\Jasper\Exceptions\ReportNotFoundException;

class Jasper
{
    private string $jasperStarter;
    private string $data;
    private string $command;
    private string $reportPath;

    public function __construct()
    {
        $jasperStarterPath = config('jasper.executable_path');
        if (!file_exists($jasperStarterPath)) throw new JasperStarterNotFoundException("JasperStarter not found in $jasperStarterPath");
        $this->jasperStarter = $jasperStarterPath;
    }

    public function availablePrinters(): Collection
    {
        exec("$this->jasperStarter list_printers", $printerList);
        $printerListString = implode(',', $printerList);
        return Str::of($printerListString)->afterLast('-,')->explode(',');
    }

    public function listParameters(string $report): array
    {
        $reportPath = $this->getReportPath($report);
        exec("$this->jasperStarter list_parameters $reportPath", $parameters);
        return array_map(function($parameter) {
            return Str::of($parameter)->after(' ')->beforeLast(' ')->__toString();
        },$parameters);
    }


    public function process(string $report, string $data): Jasper
    {
        $this->reportPath = $this->getReportPath($report);
        $this->data = $data;
        $this->command = "$this->jasperStarter process $this->reportPath -t json --data-file -";
        return $this;
    }

    public function withParameters(array $parameters, bool $validate): Jasper
    {
        if ($validate) $this->validateParameters($parameters);
        $this->command.= ' -P';
        foreach($parameters as $name => $value) {
            $this->command.= " $name=\"$value\"";
        }
        return $this;
    }

    public function view()
    {
        $this->command.= ' -f view';
        $this->execute();
    }

    public function print(int $numberOfCopies = 1, ?string $printerName = null)
    {
        $this->command.= " -f print -c $numberOfCopies";
        if ($printerName) $this->command.= " -N \"$printerName\"";
        $this->execute();
    }

    public function pdf(): string
    {
        $this->command.= " -f pdf -o -";
        return $this->execute();
    }

    private function getReportPath($report)
    {
        $reportPath = Storage::path($report);
        if (!file_exists($reportPath)) throw new ReportNotFoundException("Report file not found in $reportPath");
        return $reportPath;
    }

    private function validateParameters(array $parameters)
    {
        $reportParameters = $this->listParameters($this->reportPath);
        foreach ($parameters as $parameterName => $parameterValue) {
            if (! in_array($parameterName, $reportParameters)) throw new ParameterDoesntExistException("Parameter ($parameterName) doesnt exist in $this->reportPath");
        }
    }

    private function execute(): string
    {
        $descriptorspec = array(
            0 => array("pipe", "r"),
            1 => array("pipe", "w"),
        );
        $process = proc_open($this->command, $descriptorspec, $pipes);
        fwrite($pipes[0], $this->data);
        fclose($pipes[0]);
        $pdfRawData = stream_get_contents($pipes[1], 1000000);
        fclose($pipes[1]);
        proc_close($process);
        return base64_encode($pdfRawData);
    }
}
