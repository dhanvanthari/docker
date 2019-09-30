import React from 'react';
import { storiesOf } from '@storybook/react';
import { action } from '@storybook/addon-actions';
import VotingFooter from '.';

const props = {
    totalVotes: 800,
    votes: [
        {
            id: 'important',
            name: 'Essentielle',
            count: 86,
            isSelected: false,
        },
        {
            id: 'feasible',
            name: 'Réalisable',
            count: 165,
            isSelected: true,
        },
        {
            id: 'innovative',
            name: 'Innovante',
            count: 1536,
            isSelected: false,
        },
    ],
};

storiesOf('IdeaCard/VotingFooter', module)
    .addParameters({ jest: ['VotingFooter'] })
    .add('default', () => (
        <VotingFooter {...props} onSelected={action('selected vote')} onToggleVotePanel={action('toggle vote panel')} />
    ))
    .add('user has voted', () => (
        <VotingFooter
            {...props}
            onSelected={action('selected vote')}
            onToggleVotePanel={action('toggle vote panel')}
            hasUserVoted={true}
        />
    ));
