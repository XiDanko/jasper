<?php

namespace XiDanko\Jasper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use XiDanko\Jasper\Exceptions\JasperStarterNotFoundException;
use XiDanko\Jasper\Exceptions\ParameterDoesntExistException;
use XiDanko\Jasper\Exceptions\ReportNotFoundException;

class Jasper
{
    private string $jasperStarter;
    private string $command;
    private string $dbDriver, $dbUsername, $dbPassword, $dbHost, $dbPort, $dbName;

    public function __construct()
    {
        $jasperStarterPath = config('jasper.executable_path');
        if (!file_exists($jasperStarterPath)) {
            throw new JasperStarterNotFoundException("JasperStarter not found in $jasperStarterPath");
        };
        $this->jasperStarter = $jasperStarterPath;

        $this->dbDriver = config('database.default') === 'pgsql' ? 'postgres' : config('database.default');
        $this->dbUsername = config('database.connections.' . config('database.default') . '.username');
        $this->dbPassword = config('database.connections.' . config('database.default') . '.password');
        $this->dbHost = config('database.connections.' . config('database.default') . '.host');
        $this->dbPort = config('database.connections.' . config('database.default') . '.port');
        $this->dbName = config('database.connections.' . config('database.default') . '.database');
    }


    public function process(string $reportPath, array $parameters = []): Jasper
    {
        if (!file_exists($reportPath)) throw new ReportNotFoundException("Report file not found in $reportPath");

        $this->command = "\"$this->jasperStarter\" process \"$reportPath\" -t $this->dbDriver -u $this->dbUsername -p $this->dbPassword -H $this->dbHost -n $this->dbName --db-port $this->dbPort";

        if (!empty($parameters)) {
            $this->command.= ' -P';
            foreach($parameters as $name => $value) {
                $this->command.= " $name=\"$value\"";
            }
        }
        return $this;
    }

    public function view(): void
    {
        $this->command.= ' -f view';
        $this->execute();
    }

    public function base64Pdf(): string
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'jasperreport_');

        $this->command.= " -f pdf -o \"$tempPath\"";

        $this->execute();
        $data = base64_encode(file_get_contents($tempPath . '.pdf'));
        unlink($tempPath . '.pdf');
        return $data;
    }

    private function execute(): array
    {
        exec($this->command, $output);
        return $output;
    }
}
