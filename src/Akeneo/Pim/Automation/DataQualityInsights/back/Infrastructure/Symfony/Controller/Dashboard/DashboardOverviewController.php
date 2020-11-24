<?php

declare(strict_types=1);

namespace Akeneo\Pim\Automation\DataQualityInsights\Infrastructure\Symfony\Controller\Dashboard;

use Akeneo\Pim\Automation\DataQualityInsights\Domain\Query\Dashboard\GetDashboardRatesQueryInterface;
use Akeneo\Pim\Automation\DataQualityInsights\Domain\ValueObject\CategoryCode;
use Akeneo\Pim\Automation\DataQualityInsights\Domain\ValueObject\ChannelCode;
use Akeneo\Pim\Automation\DataQualityInsights\Domain\ValueObject\FamilyCode;
use Akeneo\Pim\Automation\DataQualityInsights\Domain\ValueObject\LocaleCode;
use Akeneo\Pim\Automation\DataQualityInsights\Domain\ValueObject\TimePeriod;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DashboardOverviewController
{
    private GetDashboardRatesQueryInterface $getDashboardRatesQuery;

    public function __construct(GetDashboardRatesQueryInterface $getDashboardRatesQuery)
    {
        $this->getDashboardRatesQuery = $getDashboardRatesQuery;
    }

    public function __invoke(Request $request, string $channel, string $locale, string $timePeriod)
    {
        try {
            if ($request->query->has('category')) {
                $category = new CategoryCode($request->query->get('category'));
                $rates = $this->getDashboardRatesQuery->byCategory(new ChannelCode($channel), new LocaleCode($locale), new TimePeriod($timePeriod), $category);
            } elseif ($request->query->has('family')) {
                $family = new FamilyCode($request->query->get('family'));
                $rates = $this->getDashboardRatesQuery->byFamily(new ChannelCode($channel), new LocaleCode($locale), new TimePeriod($timePeriod), $family);
            } else {
                $rates = $this->getDashboardRatesQuery->byCatalog(new ChannelCode($channel), new LocaleCode($locale), new TimePeriod($timePeriod));
            }
        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        if (empty($rates)) {
            return new JsonResponse([]);
        }

        return new JsonResponse($rates->toArray());
    }
}
