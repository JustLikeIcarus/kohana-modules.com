<?php defined('SYSPATH') or die('No direct script access.');

class Model_Module extends ORM
{
    protected $_created_column = array('column' => 'created_at', 'format' => TRUE);
    protected $_updated_column = array('column' => 'updated_at', 'format' => TRUE);
    
    protected $_sorting = array('name' => 'ASC');
    
    protected $_rules = array
    (
        'name' => array('not_empty' => array()),
        'user' => array('not_empty' => array()),
    );
    
    protected $_filters = array
    (
        TRUE => array('trim' => array()),
    );
    
    public function __get($name)
    {
        if ($name == 'tags_array')
        {
            return explode(':', $this->tags);
        }
        
        return parent::__get($name);
    }

    /**
     * Refreshes the module's GitHub repository metadata locally.
     */
    public function refresh_github_metadata()
    {
        $repo = Github::instance()->getRepoApi()->show($this->username, $this->name);

        $this->description   = $repo['description'];
        $this->homepage      = $repo['homepage'];
        $this->forks         = $repo['forks'];
        $this->watchers      = $repo['watchers'];
        $this->fork          = $repo['fork'];
        $this->has_wiki      = $repo['has_wiki'];
        $this->has_issues    = $repo['has_issues'];
        $this->has_downloads = $repo['has_downloads'];
        $this->open_issues   = $repo['open_issues'];
        
        $repo_tags = Github::instance()->getRepoApi()->getRepoTags($this->username, $this->name);
        $tags = array_keys($repo_tags);
        
        $this->tags = empty($tags) ? NULL : implode(':', $tags);
        
        $this->save();
    }
}