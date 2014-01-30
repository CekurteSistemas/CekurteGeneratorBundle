<?php

namespace Cekurte\GeneratorBundle\Tests;

use FOS\UserBundle\Model\User;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Permite a utilização de usuários do bundle FOSUserBundle durante os testes.
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 1.0
 */
abstract class UserTestCase extends ContainerTestCase
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @return \FOS\UserBundle\Doctrine\UserManager
     */
    public function getUserManager()
    {
        return $this->getContainer()->get('fos_user.user_manager');
    }

    /**
     * Recupera ou cria um usuário na base de dados
     *
     * @param  string $username
     * @param  string $role
     * @param  array  $fields os campos adicionais da entidade, exemplo: array('setNome' => 'João Paulo Cercal')
     *
     * @return User
     */
    public function getUser($username = 'test', $role = null, array $fields)
    {
        $this->user = $this->getUserManager()->findUserByUsername($username);

        if (!isset($this->user)) {

            $user = $this->getUserManager()->createUser();

            $user->setEnabled(true);
            $user->setUsername($username);
            $user->setEmail($username . '@fakemail.com');
            $user->setPlainPassword($username);

            foreach ($fields as $method => $value) {
                $user->{$method}($value);
            }

            $this->getUserManager()->updatePassword($user);

            if (isset($role)) {
                $user->addRole($role);
            }

            $this->getUserManager()->updateUser($user);

            $this->user = $user;
        }

        return $this->user;
    }

    /**
     * @param  User $user
     *
     * @return User|null
     *
     * @throws \LogicException
     */
    public function login(User $user)
    {
        $client     = $this->createClient();

        $token      = new UsernamePasswordToken($user, 'password', 'main', $user->getRoles());

        $session    = $this->getContainer()->get('session');

        $client->getContainer()->get('security.context')->setToken($token);

        $session->set('_security_main', serialize($token));

        if (!$this->getContainer()->has('security.context')) {
            throw new \LogicException('O SecurityBundle não está registrado para esta aplicação.');
        }

        if (null === $token = $this->getContainer()->get('security.context')->getToken()) {
            return null;
        }

        if (!is_object($user = $token->getUser())) {
            return null;
        }

        return $user;
    }
}