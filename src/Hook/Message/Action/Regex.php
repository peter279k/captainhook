<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Hook\Message\Action;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Exception\ActionFailed;
use CaptainHook\App\Hook\Action;
use SebastianFeldmann\Git\Repository;

/**
 * Class Regex
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 1.0.0
 */
class Regex implements Action
{
    /**
     * Execute the configured action.
     *
     * @param  \CaptainHook\App\Config         $config
     * @param  \CaptainHook\App\Console\IO     $io
     * @param  \SebastianFeldmann\Git\Repository             $repository
     * @param  \CaptainHook\App\Config\Action  $action
     * @throws \Exception
     */
    public function execute(Config $config, IO $io, Repository $repository, Config\Action $action)
    {
        $regex      = $this->getRegex($action->getOptions());
        $errorMsg   = $this->getErrorMessage($action->getOptions());
        $successMsg = $this->getSuccessMessage($action->getOptions());
        $matches    = [];

        if (!preg_match($regex, $repository->getCommitMsg()->getContent(), $matches)) {
            throw ActionFailed::withMessage(sprintf($errorMsg, $regex));
        }

        $io->write(sprintf($successMsg, $matches[0]));
    }

    /**
     * Extract regex from options array.
     *
     * @param  \CaptainHook\App\Config\Options $options
     * @return string
     * @throws \CaptainHook\App\Exception\ActionFailed
     */
    protected function getRegex(Config\Options $options)
    {
        $regex = $options->get('regex');
        if (empty($regex)) {
            throw ActionFailed::withMessage('No regex option');
        }
        return $regex;
    }

    /**
     * Determine the error message to use.
     *
     * @param  \CaptainHook\App\Config\Options $options
     * @return string
     */
    protected function getErrorMessage(Config\Options $options)
    {
        return $options->get('error') ?? 'Commit message did not match regex: %s';
    }

    /**
     * Determine the error message to use.
     *
     * @param  \CaptainHook\App\Config\Options $options
     * @return string
     */
    protected function getSuccessMessage(Config\Options $options)
    {
        return $options->get('success') ?? 'Found matching pattern: %s';
    }
}
