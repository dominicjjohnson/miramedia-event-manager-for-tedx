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
        },
        per_page: {
            type: 'string',
            default: '-1', // -1 means ALL
        },
        random_order: {
            type: 'boolean',
            default: false,
        }
    },
    edit: ({ attributes, setAttributes }) => {
        const [person_types, setPerson_types] = useState([]);
        const [loading, setLoading] = useState(true);
        const [people, setPeople] = useState([]);
        const [loadingPeople, setLoadingPeople] = useState(false);
        const [hasUserSelected, setHasUserSelected] = useState(false);
        const { InspectorControls } = wp.blockEditor;
        const { PanelBody } = wp.components;

        // Log the current person_type attribute when the block editor loads
        useEffect(() => {
            // Only log once on mount
            // eslint-disable-next-line no-console
            console.log("People Showcase block: current type attribute is", attributes.person_type);
            // If this block already has a saved person_type value (from being previously saved),
            // automatically trigger the preview
            if (attributes.person_type !== '' && attributes.person_type !== undefined) {
                setHasUserSelected(true);
            }
        }, []);

        useEffect(() => {
            // Fetch person types from REST API for taxonomy 'person_type'
            wp.apiFetch({ path: '/wp/v2/person_type?per_page=100' })
                .then((data) => {
                    const options = data.map(term => ({
                        label: term.name,
                        value: term.slug,
                        id: term.id
                    }));
                    setPerson_types(options);
                    setLoading(false);
                })
                .catch((error) => {
                    console.error('Error fetching person types:', error);
                    setPerson_types([]);
                    setLoading(false);
                });
        }, []);

        // Fetch people preview when user has selected
        useEffect(() => {
            if (!hasUserSelected) return;

            setLoadingPeople(true);

            const perPage = attributes.per_page || '-1';
            const perPageParam = perPage === '-1' ? '100' : perPage; // Use 100 for preview when ALL is selected

            if (attributes.person_type) {
                // Find the ID for the selected slug
                const selectedType = person_types.find(type => type.value === attributes.person_type);
                if (selectedType && selectedType.id) {
                    // Fetch people filtered by type using the people-filtered endpoint
                    const apiPath = '/wp/v2/people-filtered?person_type=' + selectedType.id + '&per_page=' + perPageParam;
                    wp.apiFetch({ path: apiPath })
                        .then((data) => {
                            setPeople(data);
                            setLoadingPeople(false);
                        })
                        .catch((error) => {
                            console.error('Error fetching people:', error);
                            setPeople([]);
                            setLoadingPeople(false);
                        });
                } else {
                    setPeople([]);
                    setLoadingPeople(false);
                }
            } else {
                // Fetch all people using the people-filtered endpoint
                wp.apiFetch({ path: '/wp/v2/people-filtered?per_page=' + perPageParam })
                    .then((data) => {
                        setPeople(data);
                        setLoadingPeople(false);
                    })
                    .catch((error) => {
                        console.error('Error fetching people:', error);
                        setPeople([]);
                        setLoadingPeople(false);
                    });
            }
        }, [attributes.person_type, attributes.per_page, hasUserSelected, person_types]);

        // Find the selected person type name for display
        const selectedTypeName = attributes.person_type === '' || !attributes.person_type
            ? 'Display All'
            : person_types.find(type => type.value === attributes.person_type)?.label || 'Display All';

        return createElement(
            wp.element.Fragment,
            null,
            // Sidebar panel controls
            createElement(
                InspectorControls,
                null,
                createElement(
                    PanelBody,
                    { title: 'People Settings', initialOpen: true },
                    loading
                        ? createElement('p', null, 'Loading person types...')
                        : createElement(wp.element.Fragment, null,
                            createElement(SelectControl, {
                                label: "Person Type",
                                value: attributes.person_type || '',
                                options: [{ label: 'Display All', value: '' }, ...person_types],
                                onChange: (value) => {
                                    setAttributes({ person_type: value });
                                    setHasUserSelected(true);
                                },
                                __nextHasNoMarginBottom: true,
                                __next40pxDefaultSize: true
                            }),
                            createElement(SelectControl, {
                                label: "Number to Display",
                                value: attributes.per_page || '-1',
                                options: [
                                    { label: 'ALL', value: '-1' },
                                    { label: '3', value: '3' },
                                    { label: '6', value: '6' },
                                    { label: '9', value: '9' },
                                    { label: '12', value: '12' }
                                ],
                                onChange: (value) => {
                                    setAttributes({ per_page: value });
                                },
                                __nextHasNoMarginBottom: true,
                                __next40pxDefaultSize: true
                            }),
                            createElement(wp.components.CheckboxControl, {
                                label: "Display in Random Order",
                                checked: attributes.random_order || false,
                                onChange: (value) => {
                                    setAttributes({ random_order: value });
                                }
                            })
                        )
                )
            ),
            // Main editor preview
            createElement(
                'div',
                useBlockProps(),
                !hasUserSelected
                    ? createElement('div', {
                        style: {
                            padding: '20px',
                            border: '2px dashed #ccc',
                            borderRadius: '4px',
                            backgroundColor: '#f9f9f9',
                            textAlign: 'center'
                        }
                    },
                        createElement('p', { style: { margin: '0 0 10px 0', fontWeight: 'bold' } }, 'People Showcase Block'),
                        createElement('p', { style: { margin: '0', fontSize: '14px', color: '#888' } },
                            'Select a person type from the sidebar to preview'
                        )
                    )
                    : loadingPeople
                        ? createElement('div', {
                            style: {
                                padding: '20px',
                                border: '1px solid #ddd',
                                borderRadius: '4px',
                                backgroundColor: '#f9f9f9'
                            }
                        },
                            createElement('p', null, 'Loading people preview...')
                        )
                        : createElement('div', {
                            style: {
                                padding: '20px',
                                border: '1px solid #ddd',
                                borderRadius: '4px',
                                backgroundColor: '#f9f9f9'
                            }
                        },
                            createElement('p', { style: { margin: '0 0 10px 0', fontWeight: 'bold' } },
                                'People Showcase - ' + selectedTypeName
                            ),
                            people.length === 0
                                ? createElement('p', { style: { margin: '10px 0', fontStyle: 'italic', color: '#666' } },
                                    'No people found'
                                )
                                : createElement('div', {
                                    style: {
                                        display: 'grid',
                                        gridTemplateColumns: 'repeat(auto-fit, minmax(150px, 1fr))',
                                        gap: '15px',
                                        marginTop: '15px'
                                    }
                                },
                                    people.map((person) => {
                                        const photoUrl = person.featured_media_url || null;

                                        return createElement('div', {
                                            key: person.id,
                                            style: {
                                                padding: '10px',
                                                border: '1px solid #e0e0e0',
                                                borderRadius: '4px',
                                                backgroundColor: '#fff',
                                                textAlign: 'center'
                                            }
                                        },
                                            photoUrl && createElement('img', {
                                                src: photoUrl,
                                                alt: person.title.rendered,
                                                style: {
                                                    maxWidth: '100%',
                                                    height: 'auto',
                                                    maxHeight: '100px',
                                                    objectFit: 'cover',
                                                    borderRadius: '4px'
                                                }
                                            }),
                                            createElement('p', {
                                                style: {
                                                    margin: '5px 0 0 0',
                                                    fontSize: '12px',
                                                    fontWeight: 'bold',
                                                    color: '#333'
                                                }
                                            }, person.title.rendered)
                                        );
                                    })
                                ),
                            people.length > 0 && createElement('p', {
                                style: { margin: '10px 0 0 0', fontSize: '12px', color: '#888', fontStyle: 'italic' }
                            }, attributes.per_page === '-1' ? 'Showing all people' : 'Showing up to ' + attributes.per_page + ' people')
                        )
            )
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
        },
        per_page: {
            type: 'string',
            default: '-1', // -1 means ALL
        },
        random_order: {
            type: 'boolean',
            default: false,
        }
    },
    edit: ({ attributes, setAttributes }) => {
        const [years, setYears] = useState([]);
        const [loading, setLoading] = useState(true);
        const [talks, setTalks] = useState([]);
        const [loadingTalks, setLoadingTalks] = useState(false);
        const [hasUserSelected, setHasUserSelected] = useState(false);
        const { InspectorControls } = wp.blockEditor;
        const { PanelBody } = wp.components;

        // Log the current year attribute when the block editor loads
        useEffect(() => {
            // Only log once on mount
            // eslint-disable-next-line no-console
            console.log("Talks Showcase block: current year attribute is", attributes.year);
            // If this block already has a saved year value (from being previously saved),
            // automatically trigger the preview
            if (attributes.year !== '' && attributes.year !== undefined) {
                setHasUserSelected(true);
            }
        }, []);

        useEffect(() => {
            // Fetch years from REST API for taxonomy 'talk_year'
            wp.apiFetch({ path: '/wp/v2/talk_year?per_page=100' })
                .then((data) => {
                    const options = data.map(term => ({
                        label: term.name,
                        value: term.slug,
                        id: term.id
                    }));
                    setYears(options);
                    setLoading(false);
                })
                .catch((error) => {
                    console.error('Error fetching talk years:', error);
                    setYears([]);
                    setLoading(false);
                });
        }, []);

        // Fetch talks preview when user has selected
        useEffect(() => {
            if (!hasUserSelected) return;

            setLoadingTalks(true);

            const perPage = attributes.per_page || '-1';
            const perPageParam = perPage === '-1' ? '100' : perPage; // Use 100 for preview when ALL is selected

            if (attributes.year) {
                // Find the ID for the selected slug
                const selectedYear = years.find(y => y.value === attributes.year);
                if (selectedYear && selectedYear.id) {
                    // Fetch talks filtered by year using the talks-filtered endpoint
                    const apiPath = '/wp/v2/talks-filtered?talk_year=' + selectedYear.id + '&per_page=' + perPageParam;
                    console.log('Fetching talks with path:', apiPath);
                    wp.apiFetch({ path: apiPath })
                        .then((data) => {
                            console.log('Talks received:', data);
                            setTalks(data);
                            setLoadingTalks(false);
                        })
                        .catch((error) => {
                            console.error('Error fetching talks:', error);
                            setTalks([]);
                            setLoadingTalks(false);
                        });
                } else {
                    console.log('No year ID found for slug:', attributes.year);
                    setTalks([]);
                    setLoadingTalks(false);
                }
            } else {
                // Fetch all talks using the talks-filtered endpoint
                console.log('Fetching all talks');
                wp.apiFetch({ path: '/wp/v2/talks-filtered?per_page=' + perPageParam })
                    .then((data) => {
                        console.log('All talks received:', data);
                        setTalks(data);
                        setLoadingTalks(false);
                    })
                    .catch((error) => {
                        console.error('Error fetching talks:', error);
                        setTalks([]);
                        setLoadingTalks(false);
                    });
            }
        }, [attributes.year, attributes.per_page, hasUserSelected, years]);

        // Find the selected year name for display
        const selectedYearName = attributes.year === '' || !attributes.year
            ? 'Display All'
            : years.find(y => y.value === attributes.year)?.label || 'Display All';

        return createElement(
            wp.element.Fragment,
            null,
            // Sidebar panel controls
            createElement(
                InspectorControls,
                null,
                createElement(
                    PanelBody,
                    { title: 'Talk Settings', initialOpen: true },
                    loading
                        ? createElement('p', null, 'Loading years...')
                        : createElement(wp.element.Fragment, null,
                            createElement(SelectControl, {
                                label: "Year",
                                value: attributes.year || '',
                                options: [{ label: 'Display All', value: '' }, ...years],
                                onChange: (value) => {
                                    setAttributes({ year: value });
                                    setHasUserSelected(true);
                                },
                                __nextHasNoMarginBottom: true,
                                __next40pxDefaultSize: true
                            }),
                            createElement(SelectControl, {
                                label: "Number to Display",
                                value: attributes.per_page || '-1',
                                options: [
                                    { label: 'ALL', value: '-1' },
                                    { label: '3', value: '3' },
                                    { label: '6', value: '6' },
                                    { label: '9', value: '9' },
                                    { label: '12', value: '12' }
                                ],
                                onChange: (value) => {
                                    setAttributes({ per_page: value });
                                },
                                __nextHasNoMarginBottom: true,
                                __next40pxDefaultSize: true
                            }),
                            createElement(wp.components.CheckboxControl, {
                                label: "Display in Random Order",
                                checked: attributes.random_order || false,
                                onChange: (value) => {
                                    setAttributes({ random_order: value });
                                }
                            })
                        )
                )
            ),
            // Main editor preview
            createElement(
                'div',
                useBlockProps(),
                !hasUserSelected
                    ? createElement('div', {
                        style: {
                            padding: '20px',
                            border: '2px dashed #ccc',
                            borderRadius: '4px',
                            backgroundColor: '#f9f9f9',
                            textAlign: 'center'
                        }
                    },
                        createElement('p', { style: { margin: '0 0 10px 0', fontWeight: 'bold' } }, 'Talks Showcase Block'),
                        createElement('p', { style: { margin: '0', fontSize: '14px', color: '#888' } },
                            'Select a year from the sidebar to preview'
                        )
                    )
                    : loadingTalks
                        ? createElement('div', {
                            style: {
                                padding: '20px',
                                border: '1px solid #ddd',
                                borderRadius: '4px',
                                backgroundColor: '#f9f9f9'
                            }
                        },
                            createElement('p', null, 'Loading talks preview...')
                        )
                        : createElement('div', {
                            style: {
                                padding: '20px',
                                border: '1px solid #ddd',
                                borderRadius: '4px',
                                backgroundColor: '#f9f9f9'
                            }
                        },
                            createElement('p', { style: { margin: '0 0 10px 0', fontWeight: 'bold' } },
                                'Talks Showcase - ' + selectedYearName
                            ),
                            talks.length === 0
                                ? createElement('p', { style: { margin: '10px 0', fontStyle: 'italic', color: '#666' } },
                                    'No talks found'
                                )
                                : createElement('div', {
                                    style: {
                                        display: 'grid',
                                        gridTemplateColumns: 'repeat(auto-fit, minmax(150px, 1fr))',
                                        gap: '15px',
                                        marginTop: '15px'
                                    }
                                },
                                    talks.map((talk) => {
                                        const thumbnailUrl = talk.featured_media_url || null;

                                        return createElement('div', {
                                            key: talk.id,
                                            style: {
                                                padding: '10px',
                                                border: '1px solid #e0e0e0',
                                                borderRadius: '4px',
                                                backgroundColor: '#fff',
                                                textAlign: 'center'
                                            }
                                        },
                                            thumbnailUrl && createElement('img', {
                                                src: thumbnailUrl,
                                                alt: talk.title.rendered,
                                                style: {
                                                    maxWidth: '100%',
                                                    height: 'auto',
                                                    maxHeight: '100px',
                                                    objectFit: 'cover',
                                                    borderRadius: '4px'
                                                }
                                            }),
                                            createElement('p', {
                                                style: {
                                                    margin: '5px 0 0 0',
                                                    fontSize: '12px',
                                                    fontWeight: 'bold',
                                                    color: '#333'
                                                }
                                            }, talk.title.rendered)
                                        );
                                    })
                                ),
                            talks.length > 0 && createElement('p', {
                                style: { margin: '10px 0 0 0', fontSize: '12px', color: '#888', fontStyle: 'italic' }
                            }, attributes.per_page === '-1' ? 'Showing all talks' : 'Showing up to ' + attributes.per_page + ' talks')
                        )
            )
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
        },
        per_page: {
            type: 'string',
            default: '-1', // -1 means ALL
        },
        random_order: {
            type: 'boolean',
            default: false,
        }
    },
    edit: ({ attributes, setAttributes }) => {
        const [company_types, setCompany_types] = useState([]);
        const [loading, setLoading] = useState(true);
        const [companies, setCompanies] = useState([]);
        const [loadingCompanies, setLoadingCompanies] = useState(false);
        const [hasUserSelected, setHasUserSelected] = useState(false);
        const { InspectorControls } = wp.blockEditor;
        const { PanelBody } = wp.components;

        // DEBUG: Log every time the component renders
        console.log('===== RENDER: company_type =', attributes.company_type, 'hasUserSelected =', hasUserSelected);

        // Log the current person_type attribute when the block editor loads
        useEffect(() => {
            // Only log once on mount
            // eslint-disable-next-line no-console
            console.log("Company block MOUNT: current type attribute is", attributes.company_type);
            // If this block already has a saved company_type value (from being previously saved),
            // automatically trigger the preview
            if (attributes.company_type !== '' && attributes.company_type !== undefined) {
                console.log("Company block MOUNT: Setting hasUserSelected to true");
                setHasUserSelected(true);
            }
        }, []);

        useEffect(() => {
            // Fetch company types from REST API for taxonomy 'company_type'
            wp.apiFetch({ path: '/wp/v2/company_type?per_page=100' })
                .then((data) => {
                    const options = data.map(term => ({
                        label: term.name,
                        value: term.slug,
                        id: term.id
                    }));
                    setCompany_types(options);
                    setLoading(false);
                })
                .catch((error) => {
                    console.error('Error fetching company types:', error);
                    setCompany_types([]);
                    setLoading(false);
                });
        }, []);

        // Fetch companies preview when user has selected
        useEffect(() => {
            console.log('===== FETCH USEEFFECT TRIGGERED! hasUserSelected:', hasUserSelected, 'company_type:', attributes.company_type);
            if (!hasUserSelected) {
                console.log('===== FETCH USEEFFECT: Returning early because hasUserSelected is false');
                return;
            }

            setLoadingCompanies(true);
            console.log('===== FETCH USEEFFECT: Starting fetch for type:', attributes.company_type);

            const perPage = attributes.per_page || '-1';
            const perPageParam = perPage === '-1' ? '100' : perPage; // Use 100 for preview when ALL is selected

            if (attributes.company_type) {
                // Find the ID for the selected slug
                const selectedType = company_types.find(type => type.value === attributes.company_type);
                if (selectedType && selectedType.id) {
                    // Fetch companies filtered by type using the companies-filtered endpoint
                    const apiPath = '/wp/v2/companies-filtered?company_type=' + selectedType.id + '&per_page=' + perPageParam;
                    console.log('Fetching companies with path:', apiPath);
                    wp.apiFetch({ path: apiPath })
                        .then((data) => {
                            console.log('Companies received:', data);
                            setCompanies(data);
                            setLoadingCompanies(false);
                        })
                        .catch((error) => {
                            console.error('Error fetching companies:', error);
                            setCompanies([]);
                            setLoadingCompanies(false);
                        });
                } else {
                    console.log('No company type ID found for slug:', attributes.company_type);
                    setCompanies([]);
                    setLoadingCompanies(false);
                }
            } else {
                // Fetch all companies using the companies-filtered endpoint
                wp.apiFetch({ path: '/wp/v2/companies-filtered?per_page=' + perPageParam })
                    .then((data) => {
                        setCompanies(data);
                        setLoadingCompanies(false);
                    })
                    .catch((error) => {
                        console.error('Error fetching companies:', error);
                        setCompanies([]);
                        setLoadingCompanies(false);
                    });
            }
        }, [attributes.company_type, attributes.per_page, hasUserSelected, company_types]);

        // Find the selected company type name for display
        const selectedTypeName = attributes.company_type === '' || !attributes.company_type
            ? 'Display All'
            : company_types.find(type => type.value === attributes.company_type)?.label || 'Display All';

        return createElement(
            wp.element.Fragment,
            null,
            // Sidebar panel controls
            createElement(
                InspectorControls,
                null,
                createElement(
                    PanelBody,
                    { title: 'Company Settings', initialOpen: true },
                    loading
                        ? createElement('p', null, 'Loading company types...')
                        : createElement(wp.element.Fragment, null,
                            createElement(SelectControl, {
                                label: "Company Type",
                                value: attributes.company_type || '',
                                options: [{ label: 'Display All', value: '' }, ...company_types],
                                onChange: (value) => {
                                    console.log('===== ONCHANGE: Dropdown changed to:', value);
                                    console.log('===== ONCHANGE: About to call setAttributes');
                                    setAttributes({ company_type: value });
                                    console.log('===== ONCHANGE: setAttributes called');
                                    console.log('===== ONCHANGE: About to call setHasUserSelected(true)');
                                    setHasUserSelected(true);
                                    console.log('===== ONCHANGE: setHasUserSelected called');
                                },
                                __nextHasNoMarginBottom: true,
                                __next40pxDefaultSize: true
                            }),
                            createElement(SelectControl, {
                                label: "Number to Display",
                                value: attributes.per_page || '-1',
                                options: [
                                    { label: 'ALL', value: '-1' },
                                    { label: '3', value: '3' },
                                    { label: '6', value: '6' },
                                    { label: '9', value: '9' },
                                    { label: '12', value: '12' }
                                ],
                                onChange: (value) => {
                                    setAttributes({ per_page: value });
                                },
                                __nextHasNoMarginBottom: true,
                                __next40pxDefaultSize: true
                            }),
                            createElement(wp.components.CheckboxControl, {
                                label: "Display in Random Order",
                                checked: attributes.random_order || false,
                                onChange: (value) => {
                                    setAttributes({ random_order: value });
                                }
                            })
                        )
                )
            ),
            // Main editor preview
            createElement(
                'div',
                useBlockProps(),
                !hasUserSelected
                    ? createElement('div', {
                        style: {
                            padding: '20px',
                            border: '2px dashed #ccc',
                            borderRadius: '4px',
                            backgroundColor: '#f9f9f9',
                            textAlign: 'center'
                        }
                    },
                        createElement('p', { style: { margin: '0 0 10px 0', fontWeight: 'bold' } }, 'Companies Showcase Block'),
                        createElement('p', { style: { margin: '0', fontSize: '14px', color: '#888' } },
                            'Select a company type from the sidebar to preview'
                        )
                    )
                    : loadingCompanies
                        ? createElement('div', {
                            style: {
                                padding: '20px',
                                border: '1px solid #ddd',
                                borderRadius: '4px',
                                backgroundColor: '#f9f9f9'
                            }
                        },
                            createElement('p', null, 'Loading companies preview...')
                        )
                        : createElement('div', {
                            style: {
                                padding: '20px',
                                border: '1px solid #ddd',
                                borderRadius: '4px',
                                backgroundColor: '#f9f9f9'
                            }
                        },
                            createElement('p', { style: { margin: '0 0 10px 0', fontWeight: 'bold' } },
                                'Companies Showcase - ' + selectedTypeName
                            ),
                            companies.length === 0
                                ? createElement('p', { style: { margin: '10px 0', fontStyle: 'italic', color: '#666' } },
                                    'No companies found'
                                )
                                : createElement('div', {
                                    style: {
                                        display: 'grid',
                                        gridTemplateColumns: 'repeat(auto-fit, minmax(150px, 1fr))',
                                        gap: '15px',
                                        marginTop: '15px'
                                    }
                                },
                                    companies.map((company) => {
                                        const logoUrl = company.featured_media_url || null;

                                        return createElement('div', {
                                            key: company.id,
                                            style: {
                                                padding: '10px',
                                                border: '1px solid #e0e0e0',
                                                borderRadius: '4px',
                                                backgroundColor: '#fff',
                                                textAlign: 'center',
                                                display: 'flex',
                                                flexDirection: 'column',
                                                alignItems: 'center',
                                                justifyContent: 'center'
                                            }
                                        },
                                            logoUrl ? createElement('img', {
                                                src: logoUrl,
                                                alt: company.title.rendered,
                                                style: {
                                                    maxWidth: '100%',
                                                    width: '100%',
                                                    height: 'auto',
                                                    maxHeight: '100px',
                                                    objectFit: 'contain'
                                                }
                                            }) : createElement('p', {
                                                style: {
                                                    margin: '5px 0',
                                                    fontSize: '12px',
                                                    color: '#999',
                                                    fontStyle: 'italic'
                                                }
                                            }, 'No logo')
                                        );
                                    })
                                ),
                            companies.length > 0 && createElement('p', {
                                style: { margin: '10px 0 0 0', fontSize: '12px', color: '#888', fontStyle: 'italic' }
                            }, attributes.per_page === '-1' ? 'Showing all companies' : 'Showing up to ' + attributes.per_page + ' companies')
                        )
            )
        );
    },
    save: () => null, // Save function returns null to use server-side rendering.
});

