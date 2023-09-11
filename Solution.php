<?php

namespace Task;

use Introvert\ApiClient;
use Introvert\ApiException;
use Introvert\Configuration;

class Solution
{
    private Configuration $configuration;

    public function __construct(string $host)
    {
        $this->configuration = Configuration::getDefaultConfiguration();

        $this->configuration->setHost($host);
    }

    public function getTableHTML(int $from, int $to): string
    {
        $table = $this->getTable($from, $to);

        $html = "<table>";

        $html .= "<tr>";
        $html .= "<th>ID клиента в Ядре</th>";
        $html .= "<th>Название клиента</th>";
        $html .= "<th>Сумма его успешных сделок за период</th>";
        $html .= "</tr>";

        foreach ($table["client"] as $row) {
            $id = $row["id"];
            $name = $row["name"];
            $sum = $row["sum"];

            $html .= "<tr>";
            $html .= "<td>$id</td>";
            $html .= "<td>$name</td>";
            $html .= "<td>$sum</td>";
            $html .= "</tr>";
        }

        $globalSum = $table["global"];
        $html .= "<tr>";
        $html .= "<td></td>";
        $html .= "<th>Total sum:</th>";
        $html .= "<td>$globalSum</td>";
        $html .= "</tr>";

        $html .= "</table>";

        return $html;
    }

    private function getTable(int $from, int $to): array
    {
        $table = [
            'client' => [],
            'global' => 0,
        ];

        $clients = $this->getClients();

        foreach ($clients as $client) {
            $api = $this->getApiClient($client['api']);

            if (!$this->hasAccess($api)) {
                continue;
            }

            try {
                $sum = $this->calculateSum($from, $to, $api);

                $table['global'] += $sum;
                $table['client'][] = [
                    'id' => $client['id'],
                    'name' => $client['name'],
                    'sum' => $sum,
                ];
            } catch (ApiException $e) {
                echo 'Exception when calculating the sum: ', $e->getMessage(), PHP_EOL;
            }
        }

        return $table;
    }

    private function getClients(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'intrdev',
                'api' => '23bc075b710da43f0ffb50ff9e889aed',
            ],
            [
                'id' => 2,
                'name' => 'artedegrass0',
                'api' => '',
            ],
        ];
    }

    private function getApiClient(string $key): ApiClient
    {
        return new ApiClient(
            $this->configuration->setApiKey('key', $key)
        );
    }

    private function hasAccess(ApiClient $api): bool
    {
        try {
            $api->account->info();
            return true;
        } catch (ApiException $e) {
            echo 'Exception when calling account->info: ', $e->getMessage(), PHP_EOL;
            return false;
        }
    }

    /**
     * @param int $from
     * @param int $to
     * @param ApiClient $api
     *
     * @return int
     * @throws ApiException
     */
    private function calculateSum(int $from, int $to, ApiClient $api): int
    {
        $count = 250;
        $offset = 0;

        $sum = 0;

        do {
            $result = $api->lead->getAll(status: 142, ifmodif: $from, count: $count, offset: $offset);

            foreach ($result['result'] as $lead) {
                if ($lead['last_modified'] <= $to) {
                    $sum += $lead['price'];
                }
            }

            $offset += $count;
        } while ($result['count'] > 0);

        return $sum;
    }
}
