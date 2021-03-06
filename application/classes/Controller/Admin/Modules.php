<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Modules extends Controller_Admin
{
    public function action_index()
    {
        $this->template->title   = 'Modules - ';
        $this->template->content = View::factory('admin/modules/index')
            ->bind('modules', $modules);

        $modules = ORM::factory('Module')->find_all();
    }

    public function action_pending_deletion()
    {
        $this->template->title = 'Pending Deletion - Modules - ';
        $this->template->content = View::factory('admin/modules/pendingdeletion')
            ->bind('modules', $modules)
            ->bind('commands', $commands);

        $modules = ORM::factory('Module')
            ->where('flagged_for_deletion_at', 'IS NOT', NULL)
            ->find_all();

        $repo     = new Git_Repository();
        $commands = array();
        foreach (ORM::factory('Kohana_Version')->order_by('name', 'DESC')->find_all() as $version)
        {
            $commands[$version->name] = array();

            $deleted = $version
                ->modules
                ->where('flagged_for_deletion_at', 'IS NOT', NULL)
                ->find_all();

            foreach ($deleted as $module)
            {
                $url = "git://github.com/$module->username/$module->name.git";

                $submodule = $repo->submodule($version->name, 'url', $url);

                if ( ! $submodule)
                    continue;

                $commands[$version->name] = Arr::merge($commands[$version->name], $repo->remove_submodule($submodule));
            }
        }
    }
}
