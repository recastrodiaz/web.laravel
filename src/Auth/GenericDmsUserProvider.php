<?php declare(strict_types = 1);

namespace Dms\Web\Laravel\Auth;

use Dms\Common\Structure\Web\EmailAddress;
use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Exception\TypeMismatchException;
use Dms\Core\Model\Object\Entity;
use Dms\Core\Persistence\Db\Connection\IConnection;
use Dms\Core\Persistence\Db\Mapping\IOrm;
use Dms\Core\Persistence\DbRepository;
use Dms\Core\Persistence\IRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider as UserProviderInterface;
use Illuminate\Foundation\Auth\User;
use Illuminate\Hashing\BcryptHasher;

/**
 * The generic user provider class for DMS.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class GenericDmsUserProvider implements UserProviderInterface
{
    /**
     * @var IRepository
     */
    protected $repository;

    /**
     * @var array
     */
    protected $config;

    /**
     * UserProvider constructor.
     *
     * @param IOrm        $orm
     * @param IConnection $connection
     * @param array       $config
     *
     * @throws InvalidArgumentException
     */
    public function __construct(IOrm $orm, IConnection $connection, array $config)
    {
        foreach (['class', 'password', 'remember_token'] as $parameter) {
            if (empty($config[$parameter])) {
                throw InvalidArgumentException::format('config/auth.php is missing the the \'%s\' parameter', $parameter);
            }
        }

        $this->repository = new DbRepository($connection, $orm->getEntityMapper($config['class']));
        $this->config     = $config;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed $id
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($id)
    {
        return $this->repository->tryGet((int)$id);
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed  $identifier
     * @param  string $token
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        $users = $this->repository->matching(
            $this->repository->criteria()
                ->where(Entity::ID, '=', (int)$identifier)
                ->where($this->config['remember_token'], '=', $token)
        );

        return reset($users) ?: null;
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  string                                     $token
     *
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        $user = $this->validateUser($user);

        $user->setRememberToken($token);
        $this->repository->save($user);
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        $criteria = $this->criteriaFromCredentialsArray($credentials);

        $users = $this->repository->matching($criteria);

        return reset($users) ?: null;
    }

    /**
     * @param array $credentials
     *
     * @return \Dms\Core\Model\Criteria\Criteria
     */
    private function criteriaFromCredentialsArray(array $credentials)
    {
        $criteria = $this->repository->criteria();

        foreach ($credentials as $column => $value) {
            if (strpos($column, 'email') !== false) {
                $criteria->where($column, '=', new EmailAddress($value));
            } elseif (strpos($column, 'password') === false) {
                $criteria->where($column, '=', $value);
            }
        }

        return $criteria;
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  array                                      $credentials
     *
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials) : bool
    {
        $user = $this->validateUser($user);

        /** @var BcryptHasher $app */
        $app = app('hash');

        return $app->check($credentials['password'], $user->{$this->config['password']});
    }

    /**
     * @param Authenticatable $user
     *
     * @return Authenticatable
     * @throws TypeMismatchException
     */
    private function validateUser(Authenticatable $user) : Authenticatable
    {
        $userClass = $this->repository->getObjectType();

        if (!($user instanceof $userClass)) {
            throw TypeMismatchException::format('Expecting instance of %s, %s given', User::class, get_class($user));
        }

        return $user;
    }
}