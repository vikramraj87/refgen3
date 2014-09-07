<?php
namespace User\table;

use Zend\Db\Adapter\Adapter,
    Zend\Db\Adapter\AdapterAwareInterface,
    Zend\Db\TableGateway\AbstractTableGateway;
class UserSocialTable extends AbstractTableGateway implements AdapterAwareInterface
{
    protected $table = 'user_socials';

    /** @var SocialTable */
    private $socialTable;

    /**
     * Set db adapter
     *
     * @param Adapter $adapter
     * @return AdapterAwareInterface
     */
    public function setDbAdapter(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    /**
     * Fetches social data by social and social_id. Social indicates the
     * social network the user is currently logged in
     *
     * @param string $social
     * @param string $socialId
     * @return array|null
     */
    public function fetchBySocialAndSocialId($social = '', $socialId = '')
    {
        $social = $this->socialTable->fetchIdByName($social);
        if(null === $social) {
            return null;
        }

        $rowset = $this->select(array(
                'social'    => $social,
                'social_id' => $socialId
            )
        );
        $data = $rowset->current();
        if(false === $data) {
            return null;
        }
        return array(
            'userId'   => $data['user_id'],
            'socialId' => $data['social_id']
        );
    }

    /**
     * Fetches social data by user_id and social. Social indicates which social
     * network the user has currently logged in
     *
     * @param int $userId
     * @param string $social
     * @return array|null
     */
    public function fetchByUserIdAndSocial($userId = 0, $social = '')
    {
        $userId = (int) $userId;
        $social = $this->socialTable->fetchIdByName($social);
        if(null === $social) {
            return null;
        }

        $rowset = $this->select(array(
                'user_id' => $userId,
                'social' => $social
            )
        );
        $data = $rowset->current();
        if(false === $data) {
            return null;
        }
        return array(
            'userId'   => $data['user_id'],
            'socialId' => $data['social_id']
        );
    }

    /**
     * Fetches social data by user_id and social. If none exists, creates one
     *
     * @param int $userId
     * @param string $social
     * @param string $socialId
     * @return bool
     * @throws \RuntimeException
     */
    public function checkUserIdAndSocial($userId = 0, $social = '', $socialId = '')
    {
        $userId = (int) $userId;
        $social = $this->socialTable->fetchIdByName($social);
        $data = $this->select(array(
                'user_id' => $userId,
                'social'  => $social
            )
        )->current();

        if(false === $data) {
            $result = (bool) $this->insert(array(
                    'user_id'   => $userId,
                    'social'    => $social,
                    'social_id' => $socialId
                )
            );
            if(false === $result) {
                throw new \RuntimeException(sprintf('Insertion failed for user'));
            }
            return true;
        }

        if($socialId != $data['social_id']) {
            throw new \RuntimeException('Integrity constraint problem in user social table');
        }
        return true;
    }

    /**
     * Setter for socialTable
     *
     * @param SocialTable $socialTable
     */
    public function setSocialTable(SocialTable $socialTable)
    {
        $this->socialTable = $socialTable;
    }
} 