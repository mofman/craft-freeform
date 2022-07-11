<?php
/**
 * Freeform for Craft CMS.
 *
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2022, Solspace, Inc.
 *
 * @see           https://docs.solspace.com/craft/freeform
 *
 * @license       https://docs.solspace.com/license-agreement
 */

namespace Solspace\Freeform\Services;

use Solspace\Freeform\Freeform;
use Solspace\Freeform\Library\Composer\Components\Form;
use Solspace\Freeform\Library\Logging\FreeformLogger;
use Solspace\Freeform\Records\NotificationRecord;
use Solspace\Freeform\Services\Notifications\NotificationDatabaseService;
use Solspace\Freeform\Services\Notifications\NotificationFilesService;

class NotificationsService extends BaseService
{
    public const EVENT_BEFORE_SAVE = 'beforeSave';
    public const EVENT_AFTER_SAVE = 'afterSave';
    public const EVENT_BEFORE_DELETE = 'beforeDelete';
    public const EVENT_AFTER_DELETE = 'afterDelete';

    /** @var NotificationRecord[] */
    private static ?array $notificationCache = null;

    private static bool $allNotificationsLoaded = false;

    public function getAllNotifications(bool $indexById = true): array
    {
        $cacheIsNull = null === self::$notificationCache;

        if ($cacheIsNull || !self::$allNotificationsLoaded) {
            if ($cacheIsNull) {
                self::$notificationCache = [];
            }

            self::$allNotificationsLoaded = true;
            self::$notificationCache =
                \Craft::$container->get(NotificationDatabaseService::class)->getAll($indexById) +
                \Craft::$container->get(NotificationFilesService::class)->getAll($indexById);
        }

        if (!$indexById) {
            return array_values(self::$notificationCache);
        }

        return self::$notificationCache;
    }

    public function getNotificationById(mixed $id): ?NotificationRecord
    {
        if (null === self::$notificationCache || !isset(self::$notificationCache[$id])) {
            $record = \Craft::$container->get(NotificationDatabaseService::class)->getById($id);
            if (!$record) {
                $record = \Craft::$container->get(NotificationFilesService::class)->getById($id);
            }

            self::$notificationCache[$id] = $record;
        }

        return self::$notificationCache[$id];
    }

    public function requireNotification(Form $form, ?string $id, ?string $context): ?NotificationRecord
    {
        $notification = $this->getNotificationById($id);
        if (!$notification) {
            $logger = Freeform::getInstance()->logger->getLogger(FreeformLogger::EMAIL_NOTIFICATION);
            $logger->warning(
                Freeform::t(
                    'Email notification template with ID {id} not found',
                    ['id' => $id]
                ),
                [
                    'form' => $form->getName(),
                    'context' => $context,
                ]
            );
        }

        return $notification;
    }

    public function databaseNotificationCount(): int
    {
        return \count(\Craft::$container->get(NotificationDatabaseService::class)->getAll());
    }
}
