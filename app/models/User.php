<?php
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Auth\UserInterface;

/**
 * @property string name
 * @property int    admin
 * @property int    status
 */

class User extends Eloquent implements UserInterface, RemindableInterface
{

    const  ENABLE = 'true';

    const  DISABLE = 'false';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user';

    protected $guarded = array('id');

    protected $connection = 'base';

    public static function checkExistsByMail($mail)
    {
        $user = User::where("email", $mail)->first();
        if ($user && $user->exists) {
            return $user;
        }
        return false;
    }

    public static function checkExistsByID($id)
    {
        $user = User::find($id);
        if ($user && $user->exists) {
            return $user;
        }
        return false;
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail()
    {
        return $this->email;
    }

    public function group()
    {

        return $this->hasOne('ATURelationModel', 'group_id');
    }

    public function updateTimestamps()
    {
        return parent::updateTimestamps();
    }

    public function getDisplayModifyTime()
    {
        return \Utils\Time::qTime($this->updated_at);
    }
}