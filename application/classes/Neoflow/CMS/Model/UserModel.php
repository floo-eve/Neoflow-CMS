<?phpnamespace Neoflow\CMS\Model;use Neoflow\Framework\ORM\AbstractEntityModel;class UserModel extends AbstractEntityModel{    /**     * @var string     */    public static $tableName = 'users';    /**     * @var string     */    public static $primaryKey = 'user_id';    /**     * @var array     */    public static $properties = ['user_id', 'email', 'firstname', 'lastname', 'role_id'];    public function role()    {        return $this->belongsTo('\\Neoflow\\CMS\\Model\\RoleModel', 'role_id');    }    public function getPermissions()    {        $role = $this->role()->fetch();        return $role->permissions()->fetchAll();    }    public function getFullname()    {        return $this->firstname . ' ' . $this->lastname;    }}