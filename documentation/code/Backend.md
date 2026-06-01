# Exemple de fonction qui utilise search

Voici l'implémentation de la méthode permettant de récupérer les éléments liés à un courrier spécifique avec des critères de jointure et d'ordre.

```php
public function getMessagesByCourrier(int $courrierId,OrderCriteria $orderCriteria,PaginationCriteria $paginationCriteria): array
    {
        $conditions = [
            new ConditionCriteria('courrier', $courrierId, '='),
            new ConditionCriteria('createdAt', $paginationCriteria->getValue(), '<'),
        ];

        $joins = [
            new JoinCriteria('m.destinataire', 'd', 'LEFT'),
            new JoinCriteria('m.expediteur', 'e', 'LEFT'),
        ];

        $messages = $this->search($conditions, $orderCriteria, $paginationCriteria, $joins);
        return $messages;
}