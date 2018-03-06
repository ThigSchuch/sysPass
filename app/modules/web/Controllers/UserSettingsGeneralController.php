<?php
/**
 * sysPass
 *
 * @author nuxsmin
 * @link https://syspass.org
 * @copyright 2012-2018, Rubén Domínguez nuxsmin@$syspass.org
 *
 * This file is part of sysPass.
 *
 * sysPass is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * sysPass is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 *  along with sysPass.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace SP\Modules\Web\Controllers;

use SP\Http\JsonResponse;
use SP\Http\Request;
use SP\Modules\Web\Controllers\Traits\JsonTrait;
use SP\Services\User\UserService;

/**
 * Class UserSettingsGeneralController
 * @package SP\Modules\Web\Controllers
 */
class UserSettingsGeneralController extends SimpleControllerBase
{
    use JsonTrait;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * saveAction
     */
    public function saveAction()
    {
        $userData = $this->session->getUserData();

        $userPreferencesData = clone $userData->getPreferences();

        $userPreferencesData->setUserId($userData->getId());
        $userPreferencesData->setLang(Request::analyzeString('userlang'));
        $userPreferencesData->setTheme(Request::analyzeString('usertheme', 'material-blue'));
        $userPreferencesData->setResultsPerPage(Request::analyzeInt('resultsperpage', 12));
        $userPreferencesData->setAccountLink(Request::analyzeBool('account_link', false));
        $userPreferencesData->setSortViews(Request::analyzeBool('sort_views', false));
        $userPreferencesData->setTopNavbar(Request::analyzeBool('top_navbar', false));
        $userPreferencesData->setOptionalActions(Request::analyzeBool('optional_actions', false));
        $userPreferencesData->setResultsAsCards(Request::analyzeBool('resultsascards', false));

        try {
            $this->userService->updatePreferencesById($userData->getId(), $userPreferencesData);

            // Guardar las preferencias en la sesión
            $userData->setPreferences($userPreferencesData);

            $this->returnJsonResponse(JsonResponse::JSON_SUCCESS, __u('Preferencias actualizadas'));
        } catch (\Exception $e) {
            processException($e);

            $this->returnJsonResponseException($e);
        }
    }

    /**
     * initialize
     */
    protected function initialize()
    {
        $this->checks();

        $this->userService = $this->dic->get(UserService::class);
    }
}