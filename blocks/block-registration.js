
// Add the new custom category - Miramedia
wp.hooks.addFilter(
	'blocks.registerBlockType',
	'miramedia/add-custom-category',
    function (settings) {
        if (!wp.blocks.getCategories().some(category => category.slug === 'miramedia')) {
            wp.blocks.updateCategories(
                wp.blocks.getCategories().concat({
                    slug: 'miramedia',
                    title: 'Miramedia',
                    icon: 'dashicons-superhero' // Replace with any Dashicon.
                })
            );
        }
        return settings;
    }
);


// Get createElement from wp.element
const { createElement } = wp.element;

// Register People Showcase Block
wp.blocks.registerBlockType('miramedia/people-showcase', {
    title: 'People Showcase',
    icon: 'groups',
    category: 'miramedia',
    attributes: {
        person_type: {
            type: 'string',
            default: '', // Provide a default value to avoid undefined
        }
    },
    edit: ({ attributes, setAttributes }) => {
        const [person_types, setPerson_types] = useState([]);
        const [loading, setLoading] = useState(true);

        // Log the current person_type attribute when the block editor loads
        useEffect(() => {
            // Only log once on mount
            // eslint-disable-next-line no-console
            console.log("People Showcase block: current type attribute is", attributes.person_type); }, []);

        useEffect(() => {            
            // Fetch years from REST API for taxonomy 'person_type'
            wp.apiFetch({ path: '/wp/v2/person_type?per_page=100' }).then((data) => {
                const options = data.map(term => ({
                    label: term.name,
                    value: term.slug
                }));
                setPerson_types(options);
                setLoading(false);
            });
        }, []);
    
        return createElement(
            'div',
            useBlockProps(),
            loading
                ? createElement('p', null, 'Loading person types ...')
                : createElement(SelectControl, {
                    label: "Person Type",
                    value: attributes.person_type || '',
                    options: [{ label: 'Select Person Type', value: '' }, ...person_types],
                    onChange: (value) => {
                        setAttributes({ person_type: value });
                    },
                })
        );
    },
    save: () => null, // Save function returns null to use server-side rendering.
});

// Register Talks Showcase Block
const { useState, useEffect } = wp.element;
const { SelectControl } = wp.components;
const { useBlockProps } = wp.blockEditor;

wp.blocks.registerBlockType('miramedia/talks-showcase', {
    title: 'Talks Showcase',
    icon: 'admin-comments',
    category: 'miramedia',
    attributes: {
        year: {
            type: 'string',
            default: '', // Provide a default value to avoid undefined
        }
    },
    edit: ({ attributes, setAttributes }) => {
        const [years, setYears] = useState([]);
        const [loading, setLoading] = useState(true);

        // Log the current year attribute when the block editor loads
        useEffect(() => {
            // Only log once on mount
            // eslint-disable-next-line no-console
            console.log("Talks Showcase block: current year attribute is", attributes.year); }, []);

        useEffect(() => {            
            // Fetch years from REST API for taxonomy 'talk_year'
            wp.apiFetch({ path: '/wp/v2/talk_year?per_page=100' }).then((data) => {
                const options = data.map(term => ({
                    label: term.name,
                    value: term.slug
                }));
                setYears(options);
                setLoading(false);
            });
        }, []);
    
        return createElement(
            'div',
            useBlockProps(),
            loading
                ? createElement('p', null, 'Loading years...')
                : createElement(SelectControl, {
                    label: "Year",
                    value: attributes.year || '',
                    options: [{ label: 'Select year', value: '' }, ...years],
                    onChange: (value) => {
                        setAttributes({ year: value });
                    },
                })
        );
    },
    save: () => null, // Save function returns null to use server-side rendering.
});
const { TextControl } = wp.components;

wp.blocks.registerBlockType('miramedia/companies-showcase', {
    title: 'Companies Showcase',
    icon: 'building',
    category: 'miramedia',
    attributes: {
        company_type: {
            type: 'string',
            default: '', // Provide a default value to avoid undefined
        }
    },
    edit: ({ attributes, setAttributes }) => {
        const [company_types, setCompany_types] = useState([]);
        const [loading, setLoading] = useState(true);

        // Log the current person_type attribute when the block editor loads
        useEffect(() => {
            // Only log once on mount
            // eslint-disable-next-line no-console
            console.log("Company block: current type attribute is", attributes.company_type); }, []);

        useEffect(() => {            
            // Fetch years from REST API for taxonomy 'person_type'
            wp.apiFetch({ path: '/wp/v2/company_type?per_page=100' }).then((data) => {
                const options = data.map(term => ({
                    label: term.name,
                    value: term.slug
                }));
                setCompany_types(options);
                setLoading(false);
            });
        }, []);
    
        return createElement(
            'div',
            useBlockProps(),
            loading
                ? createElement('p', null, 'Loading company types ...')
                : createElement(SelectControl, {
                    label: "Company Type",
                    value: attributes.company_type || '',
                    options: [{ label: 'Select Company Type', value: '' }, ...company_types],
                    onChange: (value) => {
                        setAttributes({ company_type: value });
                    },
                })
        );
    },
    save: () => null, // Save function returns null to use server-side rendering.
});

