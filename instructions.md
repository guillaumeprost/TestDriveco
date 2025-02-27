# Backend Home Test

# Introduction

On souhaite mettre en place un moteur de calcul de prix d’une session de charge.

Le prix des charges se calcul en fonction de la durée de la recharge, exprimée en minutes.

Les clients souhaitent pouvoir définir des prix:

- Pour un ensemble de plages horaires sur un ou plusieurs jours
- Les règles de prix peuvent se superposer. Dans ces cas, celle ayant un ordre de priorité le plus grand s’applique au lieu des autres.

```php
new PriceRule(int $weekDayFrom, int $weekDayTo, int $minuteFrom, int $minuteTo, float $minutePrice, int $priority);

// exemple de règle pour un tarif de 0.24€/minute fixe sur toute la semaine
new PriceRule(1, 7, 0, 1440, 0.24, 0);

// exemple de règle supplémentaire qui monte le prix à 0.4€/minute entre 8:00 et 18:00 tout les jours de semaine:
new PriceRule(1, 7, 480, 1080, 0.4, 1);

// exemple de règle qui baisse le prix le week-end
new PriceRule(6, 7, 0, 1440, 0.18, 99);
```

# Objectif:

1. Développez une classe `PriceComputation`  qui calcul le prix en fonction d’une liste de `PriceRule`  entre une date de début et une date de fin.

```php
$computation = new PriceComputation();
$computation
    ->setFrom(new \DateTimeImmutable('2024/09/02 2am'))
    ->setTo(new \DateTimeImmutable('2024/09/02 4am'))
    ->addRule(new PriceRule(1, 7, 0, 1440, 0.24, 0))
	->addRule(new PriceRule(1, 7, 480, 1080, 0.4, 1))
	->addRule(new PriceRule(6, 7, 0, 1440, 0.18, 99))
;

$price = $computation->run();
```

1. Écrivez une suite de tests unitaires qui couvrent les cas limites
    1. Pour une seule Price Rule.
    2. Pour plusieurs Price Rule qui ne se superposent pas
    3. Pour plusieurs Price Rule qui se superposent
    4. Pour une période sur plusieurs jours.
    5. Pour une période sur plusieurs jours en changeant de semaine.
    6. etc…

1. Votre code doit être versioné sur Git et partager à Driveco
    1. soit via un repository privé sur Github
    2. soit une archive complète du projet (code, et .git)