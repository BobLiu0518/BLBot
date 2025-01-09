<?php

global $Event;
loadModule('nickname.tools');

$subject = nextArg(true) ?: getNickname($Event['user_id']);

$reply = [
    "{$subject}是什么？要理解{$subject}，首先就得知道什么是{$subject}。{$subject}之所以是{$subject}，正是因为它具备了{$subject}的特性。理解{$subject}，需要从{$subject}本身出发，围绕{$subject}展开，最终回归到{$subject}。只有透彻地理解{$subject}，我们才能真正明白什么是{$subject}。",
    "{$subject}是什么？如果要理解{$subject}，首先必须承认{$subject}就是{$subject}。{$subject}的本质决定了它是{$subject}，而研究{$subject}的意义就在于更深入地探讨{$subject}本身。{$subject}看似简单，却不乏深度；看似复杂，却又清晰明了。只有从{$subject}的角度看待{$subject}，我们才能逐渐接近{$subject}的真谛。",
    "要说{$subject}是什么，这可不是一两句话能说清楚的。因为{$subject}是{$subject}，它不等于其他任何东西，但又和其他一切息息相关。我们想要认识{$subject}，就得首先接纳{$subject}是{$subject}这个事实。只有在不断思考和接触{$subject}的过程中，我们才能对{$subject}有更深刻的理解。",
    "{$subject}为何是{$subject}？这个问题看似简单，却值得深思。{$subject}存在于我们熟悉的一切之中，又独立于其他存在。探讨{$subject}，不仅需要了解{$subject}的表象，更要挖掘{$subject}背后的本质。归根结底，只有通过理解{$subject}，我们才能找到答案：{$subject}就是{$subject}。",
    "{$subject}是一种什么样的存在？这个问题的答案或许已经包含在问题本身中，因为{$subject}的意义就在于{$subject}本身。无论如何定义{$subject}，它始终是独特的、不可替代的{$subject}。研究{$subject}，是为了揭示{$subject}的内涵，而这也正是我们认识{$subject}的起点和终点。",
    "提到{$subject}，人们首先想到的往往是{$subject}的名字，但{$subject}绝不仅仅是一个名字。它是一个存在，一个概念，一个需要被理解和探讨的主题。{$subject}的本质藏在它的内在逻辑中，而探索{$subject}的过程，就是不断揭示{$subject}为何是{$subject}的过程。",
];

replyAndLeave($reply[array_rand($reply)]);
