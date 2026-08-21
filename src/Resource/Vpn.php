<?php

declare(strict_types=1);

namespace BillingServ\Resource;

/** VPN module: branding, servers and connections. */
final class Vpn extends Resource
{
    /** GET /vpn/branding/get */
    public function getBranding(): array
    {
        return $this->client->get('/vpn/branding/get');
    }

    /**
     * POST /vpn/branding/create.
     *
     * @param array<string, mixed> $data
     */
    public function createBranding(array $data): array
    {
        return $this->client->post('/vpn/branding/create', $data);
    }

    /**
     * POST /vpn/servers/create. Data: country_id, server_name, ip_address, ssh_port, ssh_username,
     * ssh_password, max_connection, vpn_type, plus optional port/protocol fields.
     *
     * @param array<string, mixed> $data
     */
    public function createServer(array $data): array
    {
        return $this->client->post('/vpn/servers/create', $data);
    }

    /**
     * POST /vpn/servers/update.
     *
     * @param array<string, mixed> $data
     */
    public function updateServer(int $id, array $data): array
    {
        return $this->client->post('/vpn/servers/update', ['id' => $id] + $data);
    }

    /** POST /vpn/servers/delete */
    public function deleteServer(int $id): array
    {
        return $this->client->post('/vpn/servers/delete', ['id' => $id]);
    }

    /** POST /vpn/servers/get */
    public function getServer(int $id): array
    {
        return $this->client->post('/vpn/servers/get', ['id' => $id]);
    }

    /** GET /vpn/servers/list */
    public function listServers(?int $perPage = null): array
    {
        return $this->client->get('/vpn/servers/list', array_filter(['per_page' => $perPage]));
    }

    /** POST /vpn/servers/deploy */
    public function deployServer(int $id, string $deployPassword): array
    {
        return $this->client->post('/vpn/servers/deploy', ['id' => $id, 'password_deploy' => $deployPassword]);
    }

    /** POST /vpn/connection/get */
    public function getConnection(int $serverId, ?string $deviceInfo = null): array
    {
        $data = ['server_id' => $serverId];
        if ($deviceInfo !== null) {
            $data['device_info'] = $deviceInfo;
        }

        return $this->client->post('/vpn/connection/get', $data);
    }

    /** POST /vpn/disconnect */
    public function disconnect(int $connectionId): array
    {
        return $this->client->post('/vpn/disconnect', ['connection_id' => $connectionId]);
    }

    /** POST /vpn/get/details */
    public function getResultDetails(string $result): array
    {
        return $this->client->post('/vpn/get/details', ['result' => $result]);
    }

    /** POST /vpn/vpn-details/get */
    public function getConnectionDetails(int $id, int $connectionId): array
    {
        return $this->client->post('/vpn/vpn-details/get', ['id' => $id, 'connection_id' => $connectionId]);
    }
}
