<?php

namespace Niwee\Trident\Core;

use Niwee\Trident\Core\Database;

/**
 * Projects
 */
final class Projects
{
    /**
     * @var \Niwee\Trident\Core\Database
     */
    private $db;

    public function __construct()
    {
        $this->db = new Database();
        $db = $this->db;
    }

    /**
     * Get all projects
     */
    public static function getAll()
    {
        $db = new Database();
        $self = new self();

        // Return message if no project is found
        if ($self->count() == 0)
        {
            return "Aucun projet n'a été trouvé";
        }

        // Select all of the projects with joins
        return $db->select(
            'projects',
            [
                '[>]users' => ['projects.assigned_to' => 'id'],
                '[>]companies' => ['projects.company_id' => 'id'],
                '[>]filters' => ['projects.filter_id' => 'id'],
                '[>]services' => ['projects.service_id' => 'id'],
            ],
            [
                'projects' => [
                    'projects.name(project_name)',
                    'projects.description(project_description)',
                    'projects.created_at(project_created_at)',
                    'projects.updated_at(project_updated_at)',
                    'projects.due_date(project_due_date)',
                    'projects.git_url(project_git_url)',
                    'projects.assigned_to(project_assigned_to)',
                    'projects.company_id(project_company_id)',
                    'projects.service_id(project_service_id)',
                    'projects.filter_id(project_filter_id)',
                ],
                'companies' => [
                    'companies.id(company_id)',
                    'companies.name(company_name)',
                ],
                'services' => [
                    'services.id(service_id)',
                    'services.name(service_name)',
                ],
                'filters' => [
                    'filters.id(filter_id)',
                    'filters.name(filter_name)',
                ],
                'users' => [
                    'users.id(user_id)',
                    'users.first_name(user_first_name)',
                    'users.last_name(user_last_name)',
                ],
            ]
        );
    }

    /**
     * Get the number of projects
     */
    public static function count(): ?int
    {
        $db = new Database();
        return $db->count('projects');
    }

    /**
     * Get a project by id
     */
    public static function get(int $id): ?array
    {
        $db = new Database();

        return $db->select(
            'projects',
            [
                '[>]companies' => ['company_id' => 'id'],
                '[>]services' => ['service_id' => 'id'],
                '[>]filters' => ['filter_id' => 'id'],
                '[>]users' => ['assigned_to' => 'id'],
            ],
            [
                'projects' => [
                    'projects.name',
                    'projects.description',
                    'projects.created_at',
                    'projects.assigned_to',
                    'projects.due_date',
                    'projects.git_url',
                    'projects.assigned_to',
                    'projects.company_id',
                    'projects.service_id',
                    'projects.filter_id',
                ],
                'companies' => [
                    'companies.id',
                    'companies.name'
                ],
                'services' => [
                    'services.id',
                    'services.name'
                ],
                'filters' => [
                    'filters.id',
                    'filters.name'
                ]
            ],
            [
                'projects.id' => $id
            ]
        );
    }
}
