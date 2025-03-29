<?php

namespace Modules\Utility\Libraries\Types;

use ReflectionClass;


class WordConnectionType
{
    const SYNONYM = 'synonym';
    const ANTONYM = 'antonym';
    const HOMOPHONE = 'Homophone';
    const HYPERNYM = 'hypernym';
    const INSTANCE_HYPERNYM = 'instance_hypernym';
    const HYPONYM = 'hyponym';
    const INSTANCE_HYPONYM = 'instance_hyponym';
    const MEMBER_HOLONYM = 'member_holonym';
    const SUBSTANCE_HOLONYM = 'substance_holonym';
    const PART_HOLONYM = 'part_holonym';
    const MEMBER_MERONYM = 'member_meronym';
    const SUBSTANCE_MERONYM = 'substance_meronym';
    const PART_MERONYM = 'part_meronym';
    const ATTRIBUTE = 'attribute';
    const DERIVATIONALLY_RELATED_FORM = 'derivationally_related_form';
    const DOMAIN_OF_SYNSET_TOPIC = 'domain_of_synset_topic';
    const MEMBER_OF_THIS_DOMAIN_TOPIC = 'member_of_this_domain_topic';
    const DOMAIN_OF_SYNSET_REGION = 'domain_of_synset_region';
    const MEMBER_OF_THIS_DOMAIN_REGION = 'member_of_this_domain_region';
    const DOMAIN_OF_SYNSET_USAGE = 'domain_of_synset_usage';
    const MEMBER_OF_THIS_DOMAIN_USAGE = 'member_of_this_domain_usage';
    const ENTAILMENT = 'entailment';
    const CAUSE = 'cause';
    const ALSO_SEE = 'also_see';
    const VERB_GROUP = 'verb_group';
    const SIMILAR_TO = 'similar_to';
    const PARTICIPLE_OF_VERB = 'participle_of_verb';
    const DOMAIN_OF_SYNSET = 'domain_of_synset';
    const MEMBER_OF_THIS_DOMAIN = 'member_of_this_domain';
    const PERTAINYM = 'pertainym';
    const DERIVED_FROM_ADJECTIVE = 'derived_from_adjective';

    static function all() {
        $reflection = new ReflectionClass(static::class);
        return $reflection->getConstants();
    }
}
