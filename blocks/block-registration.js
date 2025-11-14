
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
            // Check if this is an existing block with a saved selection (not just the default empty string)
            // We need to check if hasUserSelected was previously set, but since we can't persist that,
            // we'll rely on the user clicking the dropdown to trigger the preview
        }, []);

        useEffect(() => {
            // Fetch person types from REST API for taxonomy 'person_type'
            wp.apiFetch({ path: '/wp/v2/person_type?per_page=100' })
                .then((data) => {
                    const options = data.map(term => ({
                        label: term.name,
                        value: term.slug
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

            if (attributes.person_type) {
                // Fetch people filtered by type
                wp.apiFetch({ path: '/wp/v2/person_type?slug=' + attributes.person_type })
                    .then((typeTerms) => {
                        if (typeTerms.length > 0) {
                            const apiPath = '/wp/v2/person?per_page=3&_embed&person_type=' + typeTerms[0].id;
                            return wp.apiFetch({ path: apiPath });
                        } else {
                            // Type not found, return empty array
                            return [];
                        }
                    })
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
                // Fetch all people
                wp.apiFetch({ path: '/wp/v2/person?per_page=3&_embed' })
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
        }, [attributes.person_type, hasUserSelected]);

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
                        : createElement(SelectControl, {
                            label: "Person Type",
                            value: attributes.person_type || '',
                            options: [{ label: 'Display All', value: '' }, ...person_types],
                            onChange: (value) => {
                                setAttributes({ person_type: value });
                                setHasUserSelected(true);
                            },
                            __nextHasNoMarginBottom: true,
                            __next40pxDefaultSize: true
                        })
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
                                        const photoUrl = person._embedded && person._embedded['wp:featuredmedia']
                                            ? person._embedded['wp:featuredmedia'][0].source_url
                                            : null;

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
                            }, 'Showing up to 3 people')
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
            // Check if this is an existing block with a saved selection (not just the default empty string)
            // We need to check if hasUserSelected was previously set, but since we can't persist that,
            // we'll rely on the user clicking the dropdown to trigger the preview
        }, []);

        useEffect(() => {
            // Fetch years from REST API for taxonomy 'talk_year'
            wp.apiFetch({ path: '/wp/v2/talk_year?per_page=100' })
                .then((data) => {
                    const options = data.map(term => ({
                        label: term.name,
                        value: term.slug
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

            if (attributes.year) {
                // Fetch talks filtered by year
                console.log('Fetching talks for year:', attributes.year);
                wp.apiFetch({ path: '/wp/v2/talk_year?slug=' + attributes.year })
                    .then((yearTerms) => {
                        console.log('Year terms found:', yearTerms);
                        if (yearTerms.length > 0) {
                            const apiPath = '/wp/v2/talk?per_page=3&_embed&talk_year=' + yearTerms[0].id;
                            console.log('Fetching talks with path:', apiPath);
                            return wp.apiFetch({ path: apiPath });
                        } else {
                            // Year not found, return empty array
                            console.log('No year term found for slug:', attributes.year);
                            return [];
                        }
                    })
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
                // Fetch all talks
                console.log('Fetching all talks');
                wp.apiFetch({ path: '/wp/v2/talk?per_page=3&_embed' })
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
        }, [attributes.year, hasUserSelected]);

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
                        : createElement(SelectControl, {
                            label: "Year",
                            value: attributes.year || '',
                            options: [{ label: 'Display All', value: '' }, ...years],
                            onChange: (value) => {
                                setAttributes({ year: value });
                                setHasUserSelected(true);
                            },
                            __nextHasNoMarginBottom: true,
                            __next40pxDefaultSize: true
                        })
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
                                        const thumbnailUrl = talk._embedded && talk._embedded['wp:featuredmedia']
                                            ? talk._embedded['wp:featuredmedia'][0].source_url
                                            : null;

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
                            }, 'Showing up to 3 talks')
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

        // Log the current person_type attribute when the block editor loads
        useEffect(() => {
            // Only log once on mount
            // eslint-disable-next-line no-console
            console.log("Company block: current type attribute is", attributes.company_type);
            // Check if this is an existing block with a saved selection (not just the default empty string)
            // We need to check if hasUserSelected was previously set, but since we can't persist that,
            // we'll rely on the user clicking the dropdown to trigger the preview
        }, []);

        useEffect(() => {
            // Fetch company types from REST API for taxonomy 'company_type'
            wp.apiFetch({ path: '/wp/v2/company_type?per_page=100' })
                .then((data) => {
                    const options = data.map(term => ({
                        label: term.name,
                        value: term.slug
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
            if (!hasUserSelected) return;

            setLoadingCompanies(true);

            if (attributes.company_type) {
                // Fetch companies filtered by type
                wp.apiFetch({ path: '/wp/v2/company_type?slug=' + attributes.company_type })
                    .then((typeTerms) => {
                        if (typeTerms.length > 0) {
                            const apiPath = '/wp/v2/company?per_page=3&_embed&company_type=' + typeTerms[0].id;
                            return wp.apiFetch({ path: apiPath });
                        } else {
                            // Type not found, return empty array
                            return [];
                        }
                    })
                    .then((data) => {
                        setCompanies(data);
                        setLoadingCompanies(false);
                    })
                    .catch((error) => {
                        console.error('Error fetching companies:', error);
                        setCompanies([]);
                        setLoadingCompanies(false);
                    });
            } else {
                // Fetch all companies
                wp.apiFetch({ path: '/wp/v2/company?per_page=3&_embed' })
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
        }, [attributes.company_type, hasUserSelected]);

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
                        : createElement(SelectControl, {
                            label: "Company Type",
                            value: attributes.company_type || '',
                            options: [{ label: 'Display All', value: '' }, ...company_types],
                            onChange: (value) => {
                                setAttributes({ company_type: value });
                                setHasUserSelected(true);
                            },
                            __nextHasNoMarginBottom: true,
                            __next40pxDefaultSize: true
                        })
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
                                        const logoUrl = company._embedded && company._embedded['wp:featuredmedia']
                                            ? company._embedded['wp:featuredmedia'][0].source_url
                                            : null;

                                        return createElement('div', {
                                            key: company.id,
                                            style: {
                                                padding: '10px',
                                                border: '1px solid #e0e0e0',
                                                borderRadius: '4px',
                                                backgroundColor: '#fff',
                                                textAlign: 'center'
                                            }
                                        },
                                            logoUrl && createElement('img', {
                                                src: logoUrl,
                                                alt: company.title.rendered,
                                                style: {
                                                    maxWidth: '100%',
                                                    height: 'auto',
                                                    maxHeight: '100px',
                                                    objectFit: 'contain'
                                                }
                                            }),
                                            createElement('p', {
                                                style: {
                                                    margin: '5px 0 0 0',
                                                    fontSize: '12px',
                                                    color: '#666'
                                                }
                                            }, company.title.rendered)
                                        );
                                    })
                                ),
                            companies.length > 0 && createElement('p', {
                                style: { margin: '10px 0 0 0', fontSize: '12px', color: '#888', fontStyle: 'italic' }
                            }, 'Showing up to 3 companies')
                        )
            )
        );
    },
    save: () => null, // Save function returns null to use server-side rendering.
});

