Bug {$post.id} is need a payment to proceed.

Bug id: {$post.id}
Status: {$status[$post.status]}
Estimated cost: {$post.cost}
Payment page link: {$status_change.site}?class=bug_public&function=bug_estimated_cost&id={$post.id}

Site: {$status_change.site}
Date: {$status_change.date}
