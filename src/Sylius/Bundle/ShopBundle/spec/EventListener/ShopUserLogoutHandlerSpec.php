<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ShopBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Storage\CartStorageInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\Security\Http\EventListener\DefaultLogoutListener;
use Symfony\Component\Security\Http\HttpUtils;

final class ShopUserLogoutHandlerSpec extends ObjectBehavior
{
    function let(
        HttpUtils $httpUtils,
        ChannelContextInterface $channelContext,
        CartStorageInterface $cartStorage
    ): void {
        $this->beConstructedWith($httpUtils, '/', $channelContext, $cartStorage);
    }

    function it_is_default_logout_success_handler(): void
    {
        $this->shouldHaveType(DefaultLogoutListener::class);
    }

    function it_implements_logout_success_handler_interface(): void
    {
        $this->shouldImplement(LogoutEvent::class);
    }

    function it_clears_cart_session_after_logging_out_and_return_default_handler_response(
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        HttpUtils $httpUtils,
        Request $request,
        Response $response,
        CartStorageInterface $cartStorage
    ): void {
        $channelContext->getChannel()->willReturn($channel);

        $cartStorage->removeForChannel($channel)->shouldBeCalled();

        $httpUtils->createRedirectResponse($request, '/')->willReturn($response);

        $this->onLogoutSuccess($request)->shouldReturn($response);
    }
}
