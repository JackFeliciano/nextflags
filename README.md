# NEXT FLAGS

## What NEXT FLAGS is

NEXT FLAGS advises on where a person and their companies hold residence, citizenship, banking and business. It maps the combinations available to them. A client comes in with one passport and one country. The client leaves with a set of routes. The advisor prices each route and checks it against the rules in force that year. The work runs on a jurisdiction database, a matrix of pairings and an advisor who signs the recommendation.

NEXT FLAGS gives no tax or legal advice and files nothing. The demo invents every figure in the sample. Those figures belong to the demo. NEXT FLAGS publishes no data of its own.

## The countries

The jurisdiction database holds one record for each country. Each record carries a two letter code, the country name, the days its permit asks of the holder, the day count at which the country claims the holder for tax, the tax rate on foreign income, what the permit costs to keep open for a year, the years to a passport, and whether the country banks foreigners.

## The routes

The system holds the routes. Each route names a residence country, the country that holds the company, a price, a currency and the year of the rules the advisor checked. The advisor checks the route against the rules in force that year.

## The matrix

The system joins the country data and the routes into a matrix. For every route the matrix adds the days the two permits ask of one body. The matrix says whether that pair fits one year. The desk counts a year as 364 days.

## The verdicts

The matrix gives a verdict for each pair. The verdict states one result. The results are these. The two permits ask for more days than a year holds. One country files once with no split year. The company country taxes more than the residence country. The split holds for the year.

The system sums the upkeep of the permits a route uses. It names the cheapest route on the desk. It lists the routes whose rules hold in a given year.

## The signature

Every route carries a signature. The signature holds the advisor's name, the register number and the day the advisor signed the route. The advisor puts the signature under every route.

## Running it

NEXT FLAGS runs on PHP 8.2 or later. It uses no Composer and no vendor directory. Set NEXTFLAGS_BASE to /nextflags/. Start the showroom with php -S 127.0.0.1:8125 -t showroom showroom/index.php. Run the tests with php tests/run.php.

## The tests

The tests live in tests/run.php. The command php tests/run.php runs them.